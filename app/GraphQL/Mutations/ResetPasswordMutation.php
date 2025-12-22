<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;
use GraphQL\Error\Error;

class ResetPasswordMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Resolver para restablecer contraseña
     */
    public function __invoke($_, array $args)
    {
        if ($args['password'] !== $args['passwordConfirmation']) {
            throw new Error('Las contraseñas no coinciden.');
        }

        if (strlen($args['password']) < 8) {
            throw new Error('La contraseña debe tener al menos 8 caracteres.');
        }

        $success = $this->authService->resetPassword($args['token'], $args['password']);

        if (!$success) {
            throw new Error('Token inválido o expirado.');
        }

        return [
            'success' => true,
            'message' => 'Contraseña restablecida exitosamente.',
        ];
    }
}
