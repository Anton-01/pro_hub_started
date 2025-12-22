<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class CacheStatsQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener estadísticas de caché
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getStats();
    }
}
