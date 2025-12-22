<?php

namespace App\GraphQL\Mutations;

use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class RefreshCacheMutation
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
            throw new Error('No tienes permisos para refrescar la caché.');
        }

        // Invalidar primero
        $this->cacheService->invalidateCompanyCache($args['companyId'], $args['type']);

        // Calentar la caché para ese tipo
        $this->cacheService->warmCache($args['companyId']);

        return true;
    }
}
