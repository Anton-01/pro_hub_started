<?php

namespace App\GraphQL\Queries;

use App\Models\User;

class UsersQuery
{
    /**
     * Resolver para listar usuarios de una empresa
     */
    public function __invoke($_, array $args)
    {
        $query = User::forCompany($args['companyId']);

        if (isset($args['role'])) {
            $query->where('role', $args['role']);
        }

        return $query->orderBy('name')->get();
    }
}
