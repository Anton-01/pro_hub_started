<?php

namespace App\GraphQL\Queries;

use App\Services\ActivityLogService;

class ActivityLogsQuery
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Resolver para obtener logs de actividad
     */
    public function __invoke($_, array $args)
    {
        return $this->activityLogService->getByCompany(
            $args['companyId'],
            $args['limit'] ?? 50
        );
    }
}
