<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Models\Company;
use App\Services\AuthService;
use App\Services\EmailService;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class CreateUserMutation
{
    protected AuthService $authService;
    protected EmailService $emailService;

    public function __construct(AuthService $authService, EmailService $emailService)
    {
        $this->authService = $authService;
        $this->emailService = $emailService;
    }

    /**
     * Resolver para crear usuario
     */
    public function __invoke($_, array $args)
    {
        $input = $args['input'];
        $currentUser = Auth::user();

        // Verificar permisos
        if (!$currentUser->isAdmin()) {
            throw new Error('No tienes permisos para crear usuarios.');
        }

        // Verificar que solo super_admin puede crear otros admins
        if (in_array($input['role'], ['super_admin', 'admin']) && !$currentUser->isSuperAdmin()) {
            throw new Error('Solo el super administrador puede crear administradores.');
        }

        // Verificar límite de admins
        if (in_array($input['role'], ['super_admin', 'admin'])) {
            $company = Company::find($input['company_id']);
            if (!$company || !$company->canAddAdmin()) {
                throw new Error('Se ha alcanzado el límite máximo de administradores para esta empresa.');
            }
        }

        // Crear usuario
        $user = $this->authService->registerUser($input);

        // Enviar email de bienvenida
        if (in_array($user->role, ['super_admin', 'admin'])) {
            $this->emailService->sendWelcomeAdmin($user, $input['password']);
        } else {
            $this->emailService->sendWelcomeUser($user);
        }

        return $user;
    }
}
