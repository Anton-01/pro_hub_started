<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario esté autenticado y tenga rol de admin o super_admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        if (!$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso denegado.'], 403);
            }

            auth()->logout();
            return redirect()->route('admin.login')
                ->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        if (!$user->isActive()) {
            auth()->logout();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cuenta desactivada.'], 403);
            }

            return redirect()->route('admin.login')
                ->with('error', 'Tu cuenta ha sido desactivada.');
        }

        return $next($request);
    }
}
