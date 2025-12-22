<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory, HasUuid;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'user_sessions';

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
        'user_id',
        'token',
        'ip_address',
        'user_agent',
        'expires_at',
        'created_at',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si la sesión ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verificar si la sesión es válida
     */
    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Scope para sesiones activas (no expiradas)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope para sesiones expiradas
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope para buscar por token
     */
    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }
}
