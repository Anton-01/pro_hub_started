<?php

namespace App\GraphQL\Mutations;

use App\Models\Module;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UpdateModuleMutation
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para actualizar módulo
     */
    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para actualizar módulos.');
        }

        $module = Module::findOrFail($args['id']);
        $module->update($args['input']);

        // Invalidar caché
        $this->cacheService->invalidateCompanyCache($module->company_id, 'modules');

        return $module->fresh();
    }
}
