<?php

namespace App\GraphQL\Mutations;

use App\Models\Module;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class CreateModuleMutation
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para crear módulo
     */
    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para crear módulos.');
        }

        $module = Module::create($args['input']);

        // Invalidar caché
        $this->cacheService->invalidateCompanyCache($module->company_id, 'modules');

        return $module;
    }
}
