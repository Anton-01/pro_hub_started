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

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasActivityLog, HasCompanyScope;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'calendar_events';

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
        'description',
        'content',
        'event_date',
        'start_time',
        'end_time',
        'is_all_day',
        'is_recurring',
        'recurrence_rule',
        'color',
        'icon',
        'status',
        'created_by',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'is_all_day' => false,
        'is_recurring' => false,
        'color' => '#c9a227',
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
     * Relación con el usuario que creó el evento
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el día del evento
     */
    public function getDayAttribute(): int
    {
        return $this->event_date->day;
    }

    /**
     * Obtener el mes del evento
     */
    public function getMonthAttribute(): int
    {
        return $this->event_date->month;
    }

    /**
     * Obtener el año del evento
     */
    public function getYearAttribute(): int
    {
        return $this->event_date->year;
    }

    /**
     * Scope para eventos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para eventos de un mes específico
     */
    public function scopeInMonth($query, int $month, int $year)
    {
        return $query->whereMonth('event_date', $month)
                     ->whereYear('event_date', $year);
    }

    /**
     * Scope para eventos del mes actual
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('event_date', Carbon::now()->month)
                     ->whereYear('event_date', Carbon::now()->year);
    }

    /**
     * Scope para eventos de un día específico
     */
    public function scopeOnDay($query, int $day, ?int $month = null, ?int $year = null)
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;

        return $query->whereDay('event_date', $day)
                     ->whereMonth('event_date', $month)
                     ->whereYear('event_date', $year);
    }

    /**
     * Scope para eventos futuros
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', Carbon::today());
    }

    /**
     * Scope para eventos pasados
     */
    public function scopePast($query)
    {
        return $query->where('event_date', '<', Carbon::today());
    }
}
