<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasActivityLog;
use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasActivityLog, HasCompanyScope;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'modules';

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
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'company_id',
        'label',
        'description',
        'type',
        'url',
        'target',
        'modal_id',
        'icon',
        'icon_type',
        'highlight',
        'background_color',
        'is_featured',
        'sort_order',
        'group_name',
        'status',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'type' => 'link',
        'target' => '_self',
        'icon_type' => 'svg',
        'is_featured' => false,
        'sort_order' => 0,
        'status' => 'active',
    ];

    /**
     * Relación con la empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Verificar si el módulo es un enlace externo
     */
    public function isExternal(): bool
    {
        return $this->type === 'external' || $this->target === '_blank';
    }

    /**
     * Verificar si el módulo es un modal
     */
    public function isModal(): bool
    {
        return $this->type === 'modal';
    }

    /**
     * Scope para módulos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para módulos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para ordenar por sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopeInGroup($query, string $groupName)
    {
        return $query->where('group_name', $groupName);
    }
}
