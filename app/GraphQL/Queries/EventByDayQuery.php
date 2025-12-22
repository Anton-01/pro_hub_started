<?php

namespace App\GraphQL\Queries;

use App\Models\CalendarEvent;

class EventByDayQuery
{
    /**
     * Resolver para obtener evento por día
     */
    public function __invoke($_, array $args)
    {
        return CalendarEvent::forCompany($args['companyId'])
            ->active()
            ->onDay($args['day'])
            ->first();
    }
}
