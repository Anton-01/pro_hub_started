<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserActivity implements ShouldQueue
{
    use InteractsWithQueue;

    protected ActivityLogService $activityLogService;

    /**
     * Create the event listener.
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $this->activityLogService->logLogin($event->user);
    }
}
