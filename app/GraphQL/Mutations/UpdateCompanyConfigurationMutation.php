<?php

namespace App\GraphQL\Mutations;

use App\Models\CompanyConfiguration;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UpdateCompanyConfigurationMutation
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
            throw new Error('No tienes permisos para actualizar la configuración.');
        }

        $config = CompanyConfiguration::firstOrCreate(
            ['company_id' => $args['companyId']],
            []
        );

        $config->update($args['input']);

        $this->cacheService->invalidateCompanyCache($args['companyId'], 'config');

        return $config->fresh();
    }
}
