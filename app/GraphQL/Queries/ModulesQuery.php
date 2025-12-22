<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class ModulesQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener módulos de una empresa
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getModules($args['companyId']);
    }
}
