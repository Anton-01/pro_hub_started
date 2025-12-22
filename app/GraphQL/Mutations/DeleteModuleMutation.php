<?php

namespace App\GraphQL\Mutations;

use App\Models\Module;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class DeleteModuleMutation
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para eliminar módulo
     */
    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para eliminar módulos.');
        }

        $module = Module::findOrFail($args['id']);
        $companyId = $module->company_id;

        $module->delete();

        // Invalidar caché
        $this->cacheService->invalidateCompanyCache($companyId, 'modules');

        return true;
    }
}
