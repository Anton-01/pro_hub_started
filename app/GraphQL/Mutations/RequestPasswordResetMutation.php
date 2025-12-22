<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;

class RequestPasswordResetMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Resolver para solicitar restablecimiento de contraseña
     */
    public function __invoke($_, array $args)
    {
        $this->authService->requestPasswordReset($args['email']);

        // Siempre devolvemos éxito para no revelar si el email existe
        return [
            'success' => true,
            'message' => 'Si el correo existe, recibirás un enlace para restablecer tu contraseña.',
        ];
    }
}
