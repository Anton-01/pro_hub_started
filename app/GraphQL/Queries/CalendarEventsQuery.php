<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class CalendarEventsQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener eventos del calendario
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getEvents(
            $args['companyId'],
            $args['month'] ?? null,
            $args['year'] ?? null
        );
    }
}
