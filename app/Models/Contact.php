<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasActivityLog;
use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasActivityLog, HasCompanyScope;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'contacts';

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
        'name',
        'last_name',
        'department',
        'position',
        'email',
        'phone',
        'extension',
        'mobile',
        'avatar_url',
        'sort_order',
        'status',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
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
     * Obtener el nombre completo del contacto
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    /**
     * Obtener el teléfono con extensión
     */
    public function getPhoneWithExtensionAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        return $this->extension
            ? "{$this->phone} ext. {$this->extension}"
            : $this->phone;
    }

    /**
     * Scope para contactos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para ordenar por sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope para filtrar por departamento
     */
    public function scopeInDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('last_name', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%")
              ->orWhere('department', 'ilike', "%{$search}%")
              ->orWhere('position', 'ilike', "%{$search}%");
        });
    }
}
