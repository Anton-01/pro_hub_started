<?php

namespace App\GraphQL\Mutations;

use App\Models\News;
use App\Services\CacheService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class DeleteNewsMutation
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
            throw new Error('No tienes permisos para eliminar noticias.');
        }

        $news = News::findOrFail($args['id']);
        $companyId = $news->company_id;

        $news->delete();

        $this->cacheService->invalidateCompanyCache($companyId, 'news');

        return true;
    }
}
