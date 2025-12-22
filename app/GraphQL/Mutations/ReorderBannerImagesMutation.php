<?php

namespace App\GraphQL\Mutations;

use App\Models\BannerImage;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReorderBannerImagesMutation
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
            throw new Error('No tienes permisos para reordenar imágenes.');
        }

        $companyId = null;

        DB::transaction(function () use ($args, &$companyId) {
            foreach ($args['ids'] as $index => $id) {
                $image = BannerImage::find($id);
                if ($image) {
                    $image->update(['sort_order' => $index]);
                    $companyId = $image->company_id;
                }
            }
        });

        if ($companyId) {
            $this->cacheService->invalidateCompanyCache($companyId, 'banner');
        }

        return true;
    }
}
