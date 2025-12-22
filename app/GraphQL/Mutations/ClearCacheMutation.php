<?php

namespace App\GraphQL\Mutations;

use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class ClearCacheMutation
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para limpiar la caché.');
        }

        $this->cacheService->invalidateCompanyCache($args['companyId']);

        return true;
    }
}
