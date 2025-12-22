<?php

namespace App\GraphQL\Mutations;

use App\Models\BannerImage;
use App\Services\ImageService;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UploadBannerImageMutation
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
            throw new Error('No tienes permisos para subir imágenes.');
        }

        $file = $args['file'];
        $companyId = $args['companyId'];

        // Subir imagen
        $metadata = $this->imageService->uploadBanner($file, $companyId);

        // Crear registro
        $bannerImage = BannerImage::create([
            'company_id' => $companyId,
            'url' => $metadata['url'],
            'original_name' => $metadata['original_name'],
            'mime_type' => $metadata['mime_type'],
            'file_size' => $metadata['file_size'],
            'width' => $metadata['width'],
            'height' => $metadata['height'],
            'sort_order' => BannerImage::forCompany($companyId)->max('sort_order') + 1,
        ]);

        $this->cacheService->invalidateCompanyCache($companyId, 'banner');

        return $bannerImage;
    }
}
