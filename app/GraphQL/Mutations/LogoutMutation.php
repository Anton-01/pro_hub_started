<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class LogoutMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Resolver para cerrar sesión
     */
    public function __invoke($_, array $args)
    {
        $user = Auth::user();

        if ($user) {
            // Revocar el token actual
            $user->currentAccessToken()->delete();
            return true;
        }

        return false;
    }
}
