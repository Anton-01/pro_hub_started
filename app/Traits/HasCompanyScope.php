<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait para aplicar automáticamente el scope de empresa (multi-tenant)
 */
trait HasCompanyScope
{
    /**
     * Boot del trait para aplicar scope global de empresa
     */
    protected static function bootHasCompanyScope(): void
    {
        // Aplicar scope global cuando hay usuario autenticado
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });

        // Asignar automáticamente company_id al crear
        static::creating(function (Model $model) {
            if (auth()->check() && !$model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    /**
     * Scope para filtrar por empresa específica
     */
    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->withoutGlobalScope('company')->where('company_id', $companyId);
    }

    /**
     * Scope para ignorar el filtro de empresa
     */
    public function scopeWithoutCompanyScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('company');
    }
}
