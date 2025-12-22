<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     * Verifica que el usuario tenga acceso a los recursos de su empresa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado.',
            ], 401);
        }

        // Obtener company_id del request (puede venir de diferentes formas)
        $companyId = $request->input('company_id')
            ?? $request->route('company_id')
            ?? $request->header('X-Company-ID');

        // Si se especifica un company_id, verificar que el usuario pertenezca a esa empresa
        if ($companyId && $user->company_id !== $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a los recursos de esta empresa.',
            ], 403);
        }

        // Agregar company_id al request para uso posterior
        $request->merge(['company_id' => $user->company_id]);

        return $next($request);
    }
}
