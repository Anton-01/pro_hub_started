<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * Trait para registrar automáticamente la actividad de un modelo
 */
trait HasActivityLog
{
    /**
     * Boot del trait para registrar eventos del modelo
     */
    protected static function bootHasActivityLog(): void
    {
        static::created(function (Model $model) {
            $model->logActivity('create', null, $model->toArray());
        });

        static::updated(function (Model $model) {
            $model->logActivity('update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            $model->logActivity('delete', $model->toArray(), null);
        });
    }

    /**
     * Registrar actividad del modelo
     */
    public function logActivity(string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        ActivityLog::create([
            'company_id' => $this->company_id ?? (auth()->check() ? auth()->user()->company_id : null),
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $this->getTable(),
            'entity_id' => $this->id,
            'description' => $this->getActivityDescription($action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Obtener descripción de la actividad
     */
    protected function getActivityDescription(string $action): string
    {
        $entityName = class_basename($this);
        $identifier = $this->name ?? $this->title ?? $this->email ?? $this->id;

        return match ($action) {
            'create' => "Se creó {$entityName}: {$identifier}",
            'update' => "Se actualizó {$entityName}: {$identifier}",
            'delete' => "Se eliminó {$entityName}: {$identifier}",
            default => "{$action} en {$entityName}: {$identifier}",
        };
    }

    /**
     * Obtener los logs de actividad de este modelo
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'entity');
    }
}
