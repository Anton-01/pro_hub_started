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
        'text',
        'url',
        'starts_at',
        'ends_at',
        'sort_order',
        'is_priority',
        'status',
        'created_by',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'sort_order' => 'integer',
        'is_priority' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'sort_order' => 0,
        'is_priority' => false,
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

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si la noticia es prioritaria
     */
    public function isPriority(): bool
    {
        return $this->is_priority;
    }

    /**
     * Scope para noticias activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para noticias actualmente visibles
     */
    public function scopeCurrentlyVisible($query)
    {
        $now = Carbon::now();

        return $query->where('status', 'active')
                     ->where(function ($q) use ($now) {
                         $q->whereNull('starts_at')
                           ->orWhere('starts_at', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('ends_at')
                           ->orWhere('ends_at', '>=', $now);
                     });
    }

    /**
     * Scope para noticias prioritarias
     */
    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }

    /**
     * Scope para ordenar por prioridad y sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_priority')
                     ->orderBy('sort_order', 'asc');
    }
}
