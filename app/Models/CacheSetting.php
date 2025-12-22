<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CacheSetting extends Model
{
    use HasFactory, HasUuid;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'cache_settings';

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
        'modules_ttl',
        'contacts_ttl',
        'events_ttl',
        'news_ttl',
        'banner_ttl',
        'config_ttl',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'modules_ttl' => 'integer',
        'contacts_ttl' => 'integer',
        'events_ttl' => 'integer',
        'news_ttl' => 'integer',
        'banner_ttl' => 'integer',
        'config_ttl' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'modules_ttl' => 600,      // 10 minutos
        'contacts_ttl' => 600,     // 10 minutos
        'events_ttl' => 600,       // 10 minutos
        'news_ttl' => 60,          // 1 minuto
        'banner_ttl' => 600,       // 10 minutos
        'config_ttl' => 3600,      // 1 hora
    ];

    /**
     * TTL por defecto en caso de no tener configuración
     */
    public const DEFAULT_TTL = [
        'modules' => 600,
        'contacts' => 600,
        'events' => 600,
        'news' => 60,
        'banner' => 600,
        'config' => 3600,
    ];

    /**
     * Relación con la empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtener el TTL para un tipo específico
     */
    public function getTtlFor(string $type): int
    {
        $property = "{$type}_ttl";

        return $this->{$property} ?? self::DEFAULT_TTL[$type] ?? 600;
    }

    /**
     * Obtener todos los TTL como array
     */
    public function getAllTtl(): array
    {
        return [
            'modules' => $this->modules_ttl,
            'contacts' => $this->contacts_ttl,
            'events' => $this->events_ttl,
            'news' => $this->news_ttl,
            'banner' => $this->banner_ttl,
            'config' => $this->config_ttl,
        ];
    }

    /**
     * Obtener o crear la configuración de caché para una empresa
     */
    public static function getForCompany(string $companyId): self
    {
        return static::firstOrCreate(
            ['company_id' => $companyId],
            [
                'modules_ttl' => self::DEFAULT_TTL['modules'],
                'contacts_ttl' => self::DEFAULT_TTL['contacts'],
                'events_ttl' => self::DEFAULT_TTL['events'],
                'news_ttl' => self::DEFAULT_TTL['news'],
                'banner_ttl' => self::DEFAULT_TTL['banner'],
                'config_ttl' => self::DEFAULT_TTL['config'],
            ]
        );
    }
}
