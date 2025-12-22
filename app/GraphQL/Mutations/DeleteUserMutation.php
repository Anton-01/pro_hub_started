<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Auth;

class DeleteUserMutation
{
    /**
     * Resolver para eliminar usuario
     */
    public function __invoke($_, array $args)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($args['id']);

        // Verificar permisos
        if (!$currentUser->isSuperAdmin()) {
            throw new Error('Solo el super administrador puede eliminar usuarios.');
        }

        // No se puede eliminar al super_admin
        if ($user->isSuperAdmin()) {
            throw new Error('No se puede eliminar al super administrador.');
        }

        // No se puede auto-eliminar
        if ($currentUser->id === $user->id) {
            throw new Error('No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return true;
    }
}
