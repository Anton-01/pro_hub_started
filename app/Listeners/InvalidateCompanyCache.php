<?php

namespace App\Listeners;

use App\Events\CompanyConfigurationUpdated;
use App\Services\CacheService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InvalidateCompanyCache implements ShouldQueue
{
    use InteractsWithQueue;

    protected CacheService $cacheService;

    /**
     * Create the event listener.
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the event.
     */
    public function handle(CompanyConfigurationUpdated $event): void
    {
        $this->cacheService->invalidateCompanyCache($event->configuration->company_id, 'config');
    }
}
