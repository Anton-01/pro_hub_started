<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasActivityLog;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'companies';

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
        'name',
        'slug',
        'tax_id',
        'email',
        'phone',
        'address',
        'website',
        'status',
        'max_admins',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'max_admins' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación con la configuración de la empresa
     */
    public function configuration(): HasOne
    {
        return $this->hasOne(CompanyConfiguration::class);
    }

    /**
     * Relación con los usuarios de la empresa
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con los módulos de la empresa
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Relación con los eventos del calendario
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Relación con las noticias
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    /**
     * Relación con los contactos
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Relación con las imágenes del banner
     */
    public function bannerImages(): HasMany
    {
        return $this->hasMany(BannerImage::class);
    }

    /**
     * Relación con la configuración de caché
     */
    public function cacheSettings(): HasOne
    {
        return $this->hasOne(CacheSetting::class);
    }

    /**
     * Relación con los logs de actividad
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Obtener administradores de la empresa
     */
    public function admins(): HasMany
    {
        return $this->users()->whereIn('role', ['super_admin', 'admin']);
    }

    /**
     * Obtener el super administrador de la empresa
     */
    public function superAdmin(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'super_admin');
    }

    /**
     * Verificar si la empresa puede tener más administradores
     */
    public function canAddAdmin(): bool
    {
        return $this->admins()->count() < $this->max_admins;
    }

    /**
     * Scope para empresas activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para buscar por slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
