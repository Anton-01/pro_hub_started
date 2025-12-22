<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory, HasUuid;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'activity_logs';

    /**
     * La clave primaria de la tabla
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el ID es auto-incrementable
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria
     */
    protected $keyType = 'string';

    /**
     * Indica que no hay updated_at
     */
    public $timestamps = false;

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con la empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la entidad relacionada
     */
    public function getEntityAttribute()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $modelClass = $this->getModelClassFromTable($this->entity_type);

        if (!$modelClass) {
            return null;
        }

        return $modelClass::withTrashed()->find($this->entity_id);
    }

    /**
     * Mapear nombre de tabla a clase de modelo
     */
    protected function getModelClassFromTable(string $table): ?string
    {
        $mapping = [
            'users' => User::class,
            'companies' => Company::class,
            'company_configurations' => CompanyConfiguration::class,
            'modules' => Module::class,
            'calendar_events' => CalendarEvent::class,
            'news' => News::class,
            'contacts' => Contact::class,
            'banner_images' => BannerImage::class,
        ];

        return $mapping[$table] ?? null;
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para filtrar por tipo de entidad
     */
    public function scopeOfEntityType($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope para logs recientes
     */
    public function scopeRecent($query, int $limit = 50)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    /**
     * Scope para logs de los últimos N días
     */
    public function scopeLastDays($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
