<?php

namespace App\GraphQL\Mutations;

use App\Models\CalendarEvent;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class CreateCalendarEventMutation
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
            throw new Error('No tienes permisos para crear eventos.');
        }

        $input = $args['input'];
        $input['created_by'] = $currentUser->id;

        $event = CalendarEvent::create($input);

        $this->cacheService->invalidateCompanyCache($event->company_id, 'events');

        return $event;
    }
}
