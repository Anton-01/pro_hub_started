<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultModule extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'category',
        'system_name',
        'label',
        'description',
        'type',
        'url',
        'target',
        'modal_id',
        'icon',
        'icon_type',
        'background_color',
        'is_featured',
        'group_name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope para obtener solo módulos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener módulos por categoría
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para obtener módulos por sistema
     */
    public function scopeForSystem($query, string $systemName)
    {
        return $query->where('system_name', $systemName);
    }

    /**
     * Scope para ordenar por sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }
}
