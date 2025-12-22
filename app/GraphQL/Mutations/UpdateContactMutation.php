<?php

namespace App\GraphQL\Mutations;

use App\Models\Contact;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UpdateContactMutation
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
            throw new Error('No tienes permisos para actualizar contactos.');
        }

        $contact = Contact::findOrFail($args['id']);
        $contact->update($args['input']);

        $this->cacheService->invalidateCompanyCache($contact->company_id, 'contacts');

        return $contact->fresh();
    }
}
