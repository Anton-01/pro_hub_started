<?php

namespace App\GraphQL\Mutations;

use App\Models\CompanyConfiguration;
use App\Services\ImageService;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UploadCompanyLogoMutation
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
            throw new Error('No tienes permisos para subir el logo.');
        }

        $file = $args['file'];
        $companyId = $args['companyId'];

        // Subir logo
        $metadata = $this->imageService->uploadLogo($file, $companyId);

        // Actualizar configuración
        $config = CompanyConfiguration::firstOrCreate(
            ['company_id' => $companyId],
            []
        );

        $config->update([
            'logo_url' => $metadata['url'],
            'logo_width' => $metadata['width'],
            'logo_height' => $metadata['height'],
            'logo_mime_type' => $metadata['mime_type'],
            'logo_file_size' => $metadata['file_size'],
            'logo_original_name' => $metadata['original_name'],
        ]);

        $this->cacheService->invalidateCompanyCache($companyId, 'config');

        return $config->fresh();
    }
}
