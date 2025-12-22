<?php

namespace App\GraphQL\Queries;

use App\Models\Company;

class CompaniesQuery
{
    /**
     * Resolver para listar empresas
     */
    public function __invoke($_, array $args)
    {
        $query = Company::query();

        if (isset($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('slug', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return $query->active()->orderBy('name')->get();
    }
}
