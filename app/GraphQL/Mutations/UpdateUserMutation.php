<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class UpdateUserMutation
{
    /**
     * Resolver para actualizar usuario
     */
    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($args['id']);

        // Verificar permisos
        if (!$currentUser->isAdmin() && $currentUser->id !== $user->id) {
            throw new Error('No tienes permisos para actualizar este usuario.');
        }

        // Solo super_admin puede cambiar roles de admin
        if (isset($args['input']['role'])) {
            if (!$currentUser->isSuperAdmin()) {
                throw new Error('Solo el super administrador puede cambiar roles.');
            }
        }

        // No se puede cambiar el rol del super_admin
        if ($user->isSuperAdmin() && isset($args['input']['role']) && $args['input']['role'] !== 'super_admin') {
            throw new Error('No se puede cambiar el rol del super administrador.');
        }

        $user->update($args['input']);

        return $user->fresh();
    }
}
