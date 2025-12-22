<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;
use GraphQL\Error\Error;

class LoginMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Resolver para iniciar sesión
     */
    public function __invoke($_, array $args)
    {
        $result = $this->authService->attemptLogin(
            $args['email'],
            $args['password'],
            $args['companyId']
        );

        if (!$result) {
            throw new Error('Credenciales inválidas o cuenta inactiva.');
        }

        return $result;
    }
}
