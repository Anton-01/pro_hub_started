<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrimaryAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario pueda crear administradores
     * (Super Admin o Admin Primario de la empresa)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->canCreateAdmins()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Acceso denegado. Solo el administrador principal puede realizar esta acción.'
                ], 403);
            }

            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }
}
