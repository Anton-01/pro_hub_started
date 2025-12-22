<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;
use GraphQL\Error\Error;

class RefreshTokenMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Resolver para refrescar token
     */
    public function __invoke($_, array $args)
    {
        $result = $this->authService->refreshToken($args['refreshToken']);

        if (!$result) {
            throw new Error('Token de refresco inválido o expirado.');
        }

        return $result;
    }
}
