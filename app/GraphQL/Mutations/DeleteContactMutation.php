<?php

namespace App\GraphQL\Mutations;

use App\Models\Contact;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class DeleteContactMutation
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
            throw new Error('No tienes permisos para eliminar contactos.');
        }

        $contact = Contact::findOrFail($args['id']);
        $companyId = $contact->company_id;

        $contact->delete();

        $this->cacheService->invalidateCompanyCache($companyId, 'contacts');

        return true;
    }
}
