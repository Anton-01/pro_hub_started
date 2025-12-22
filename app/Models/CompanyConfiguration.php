<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyConfiguration extends Model
{
    use HasFactory, HasUuid, HasActivityLog;

    /**
     * La tabla asociada al modelo
     */
    protected $table = 'company_configurations';

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
        // Logo
        'logo_url',
        'logo_width',
        'logo_height',
        'logo_mime_type',
        'logo_file_size',
        'logo_original_name',
        // Favicon
        'favicon_url',
        // Colores del tema
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'text_color',
        'error_color',
        'success_color',
        'warning_color',
        'module_bg_color',
        'module_hover_color',
        // Configuración adicional
        'header_title',
        'footer_text',
        'show_calendar',
        'show_news_ticker',
        'show_contacts',
        // SEO
        'meta_title',
        'meta_description',
    ];

    /**
     * Atributos que deben ser casteados
     */
    protected $casts = [
        'logo_width' => 'integer',
        'logo_height' => 'integer',
        'logo_file_size' => 'integer',
        'show_calendar' => 'boolean',
        'show_news_ticker' => 'boolean',
        'show_contacts' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'logo_width' => 80,
        'logo_height' => 80,
        'primary_color' => '#c9a227',
        'secondary_color' => '#0a1744',
        'accent_color' => '#f59e0b',
        'background_color' => '#0d1b4c',
        'text_color' => '#ffffff',
        'error_color' => '#ef4444',
        'success_color' => '#10b981',
        'warning_color' => '#f59e0b',
        'module_bg_color' => '#1a3a8f',
        'module_hover_color' => '#2548a8',
        'show_calendar' => true,
        'show_news_ticker' => true,
        'show_contacts' => true,
    ];

    /**
     * Relación con la empresa
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtener el array de colores del tema
     */
    public function getThemeColorsAttribute(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
            'background' => $this->background_color,
            'text' => $this->text_color,
            'error' => $this->error_color,
            'success' => $this->success_color,
            'warning' => $this->warning_color,
            'module_bg' => $this->module_bg_color,
            'module_hover' => $this->module_hover_color,
        ];
    }

    /**
     * Obtener la información del logo
     */
    public function getLogoInfoAttribute(): ?array
    {
        if (!$this->logo_url) {
            return null;
        }

        return [
            'url' => $this->logo_url,
            'width' => $this->logo_width,
            'height' => $this->logo_height,
            'mime_type' => $this->logo_mime_type,
            'file_size' => $this->logo_file_size,
            'original_name' => $this->logo_original_name,
        ];
    }
}
