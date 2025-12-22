<?php

namespace App\GraphQL\Mutations;

use App\Models\Module;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReorderModulesMutation
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
            throw new Error('No tienes permisos para reordenar módulos.');
        }

        $companyId = null;

        DB::transaction(function () use ($args, &$companyId) {
            foreach ($args['ids'] as $index => $id) {
                $module = Module::find($id);
                if ($module) {
                    $module->update(['sort_order' => $index]);
                    $companyId = $module->company_id;
                }
            }
        });

        if ($companyId) {
            $this->cacheService->invalidateCompanyCache($companyId, 'modules');
        }

        return true;
    }
}
