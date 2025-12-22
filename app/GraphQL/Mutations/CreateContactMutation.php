<?php

namespace App\GraphQL\Mutations;

use App\Models\Contact;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class CreateContactMutation
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
            throw new Error('No tienes permisos para crear contactos.');
        }

        $contact = Contact::create($args['input']);

        $this->cacheService->invalidateCompanyCache($contact->company_id, 'contacts');

        return $contact;
    }
}
