<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasActivityLog;
use App\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class News extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasActivityLog, HasCompanyScope;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'news';

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
        'title',
        'content',
        'url',
        'published_at',
        'expires_at',
        'priority',
        'is_active',
        'status',
        'created_by',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'priority' => 0,
        'is_active' => true,
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
     * Relación con el usuario que creó la noticia
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Verificar si la noticia está activa en este momento
     */
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now();

        if ($this->status !== 'active') {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si la noticia es prioritaria
     */
    public function isHighPriority(): bool
    {
        return $this->priority <= 2;
    }

    /**
     * Scope para noticias activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('is_active', true);
    }

    /**
     * Scope para noticias actualmente visibles
     */
    public function scopeCurrentlyVisible($query)
    {
        $now = Carbon::now();

        return $query->where('status', 'active')
                     ->where(function ($q) use ($now) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>=', $now);
                     });
    }

    /**
     * Scope para noticias prioritarias
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '<=', 2);
    }

    /**
     * Scope para ordenar por prioridad y sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'asc');
    }
}
