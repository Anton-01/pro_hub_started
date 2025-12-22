<?php

namespace App\GraphQL\Queries;

use App\Services\CacheService;

class ContactsQuery
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Resolver para obtener contactos de una empresa
     */
    public function __invoke($_, array $args)
    {
        return $this->cacheService->getContacts(
            $args['companyId'],
            $args['search'] ?? null
        );
    }
}
