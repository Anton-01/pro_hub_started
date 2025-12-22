<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class BannerImagesQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener imágenes del banner
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getBanner($args['companyId']);
    }
}
