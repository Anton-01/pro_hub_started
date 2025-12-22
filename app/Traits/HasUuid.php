<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait para manejar UUIDs como clave primaria
 */
trait HasUuid
{
    /**
     * Boot del trait
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Indica que la clave primaria no es auto-incrementable
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Indica el tipo de dato de la clave primaria
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
