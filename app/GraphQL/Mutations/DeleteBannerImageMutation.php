<?php

namespace App\GraphQL\Mutations;

use App\Models\BannerImage;
use App\Services\ImageService;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class DeleteBannerImageMutation
{
    protected ImageService $imageService;
    protected CacheService $cacheService;

    public function __construct(ImageService $imageService, CacheService $cacheService)
    {
        $this->imageService = $imageService;
        $this->cacheService = $cacheService;
    }

    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para eliminar imágenes.');
        }

        $bannerImage = BannerImage::findOrFail($args['id']);
        $companyId = $bannerImage->company_id;

        // Eliminar archivo físico
        $this->imageService->delete($bannerImage->url);

        $bannerImage->delete();

        $this->cacheService->invalidateCompanyCache($companyId, 'banner');

        return true;
    }
}
