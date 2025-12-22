<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordResetToken extends Model
{
    use HasFactory, HasUuid;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'password_reset_tokens';

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
        'email',
        'token',
        'expires_at',
        'used_at',
        'created_at',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Crear un nuevo token de restablecimiento
     */
    public static function createForEmail(string $email, int $expiresInMinutes = 60): self
    {
        // Invalidar tokens anteriores para este email
        static::where('email', $email)->whereNull('used_at')->delete();

        return static::create([
            'email' => $email,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes($expiresInMinutes),
            'created_at' => now(),
        ]);
    }

    /**
     * Buscar un token válido
     */
    public static function findValidToken(string $token): ?self
    {
        return static::where('token', $token)
                     ->whereNull('used_at')
                     ->where('expires_at', '>', now())
                     ->first();
    }

    /**
     * Verificar si el token es válido
     */
    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    /**
     * Verificar si el token ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verificar si el token ya fue usado
     */
    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    /**
     * Marcar el token como usado
     */
    public function markAsUsed(): bool
    {
        return $this->update(['used_at' => now()]);
    }

    /**
     * Scope para tokens válidos
     */
    public function scopeValid($query)
    {
        return $query->whereNull('used_at')
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope para tokens expirados
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope para buscar por email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }
}
