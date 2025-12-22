<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class CompanyConfigurationQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener configuración de empresa
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getConfiguration($args['companyId']);
    }
}
