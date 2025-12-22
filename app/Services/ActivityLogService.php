<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Registrar una actividad
     */
    public function log(
        string $action,
        string $entityType,
        ?string $entityId = null,
        array $context = []
    ): ActivityLog {
        $user = auth()->user();

        return ActivityLog::create([
            'company_id' => $context['company_id'] ?? ($user ? $user->company_id : null),
            'user_id' => $context['user_id'] ?? ($user ? $user->id : null),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $context['description'] ?? $this->buildDescription($action, $entityType, $entityId),
            'old_values' => $context['old_values'] ?? null,
            'new_values' => $context['new_values'] ?? null,
            'ip_address' => $context['ip_address'] ?? Request::ip(),
            'user_agent' => $context['user_agent'] ?? Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Registrar cambio en un modelo
     */
    public function logModelChange(Model $model, string $action): ActivityLog
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'update') {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
        } elseif ($action === 'create') {
            $newValues = $model->toArray();
        } elseif ($action === 'delete') {
            $oldValues = $model->toArray();
        }

        return $this->log($action, $model->getTable(), $model->id, [
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'company_id' => $model->company_id ?? null,
        ]);
    }

    /**
     * Registrar login de usuario
     */
    public function logLogin(User $user): ActivityLog
    {
        return $this->log('login', 'users', $user->id, [
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'description' => "Usuario {$user->email} inició sesión",
        ]);
    }

    /**
     * Registrar logout de usuario
     */
    public function logLogout(User $user): ActivityLog
    {
        return $this->log('logout', 'users', $user->id, [
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'description' => "Usuario {$user->email} cerró sesión",
        ]);
    }

    /**
     * Registrar intento de login fallido
     */
    public function logFailedLogin(string $email, string $companyId): ActivityLog
    {
        return $this->log('failed_login', 'users', null, [
            'company_id' => $companyId,
            'description' => "Intento de login fallido para: {$email}",
            'new_values' => ['email' => $email],
        ]);
    }

    /**
     * Registrar restablecimiento de contraseña
     */
    public function logPasswordReset(User $user): ActivityLog
    {
        return $this->log('password_reset', 'users', $user->id, [
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'description' => "Usuario {$user->email} restableció su contraseña",
        ]);
    }

    /**
     * Obtener logs por empresa
     */
    public function getByCompany(string $companyId, int $limit = 50): Collection
    {
        return ActivityLog::forCompany($companyId)
            ->with('user:id,name,email')
            ->recent($limit)
            ->get();
    }

    /**
     * Obtener logs por usuario
     */
    public function getByUser(string $userId, int $limit = 50): Collection
    {
        return ActivityLog::forUser($userId)
            ->recent($limit)
            ->get();
    }

    /**
     * Obtener logs por tipo de entidad
     */
    public function getByEntityType(string $companyId, string $entityType, int $limit = 50): Collection
    {
        return ActivityLog::forCompany($companyId)
            ->ofEntityType($entityType)
            ->recent($limit)
            ->get();
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanup(int $daysOld = 90): int
    {
        return ActivityLog::where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Obtener resumen de actividad
     */
    public function getSummary(string $companyId, int $days = 30): array
    {
        $logs = ActivityLog::forCompany($companyId)
            ->lastDays($days)
            ->get();

        return [
            'total' => $logs->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'by_entity' => $logs->groupBy('entity_type')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'daily' => $logs->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            })->map->count(),
        ];
    }

    /**
     * Construir descripción automática
     */
    protected function buildDescription(string $action, string $entityType, ?string $entityId): string
    {
        $entityName = $this->translateEntityType($entityType);

        return match ($action) {
            'create' => "Se creó {$entityName}",
            'update' => "Se actualizó {$entityName}",
            'delete' => "Se eliminó {$entityName}",
            'restore' => "Se restauró {$entityName}",
            'login' => "Inicio de sesión",
            'logout' => "Cierre de sesión",
            'failed_login' => "Intento de login fallido",
            'password_reset' => "Restablecimiento de contraseña",
            default => "Acción '{$action}' en {$entityName}",
        };
    }

    /**
     * Traducir tipo de entidad
     */
    protected function translateEntityType(string $entityType): string
    {
        return match ($entityType) {
            'users' => 'usuario',
            'companies' => 'empresa',
            'company_configurations' => 'configuración de empresa',
            'modules' => 'módulo',
            'calendar_events' => 'evento de calendario',
            'news' => 'noticia',
            'contacts' => 'contacto',
            'banner_images' => 'imagen de banner',
            default => $entityType,
        };
    }
}
