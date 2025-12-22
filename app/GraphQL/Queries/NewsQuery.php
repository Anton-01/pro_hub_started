<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class NewsQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener noticias activas
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getNews($args['companyId']);
    }
}
