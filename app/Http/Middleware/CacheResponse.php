<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * TTL por defecto en segundos (10 minutos)
     */
    protected int $defaultTtl = 600;

    /**
     * Handle an incoming request.
     * Cachea las respuestas de endpoints públicos.
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        // Solo cachear requests GET
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // No cachear si el usuario está autenticado
        if ($request->user()) {
            return $next($request);
        }

        $ttl = $ttl ?? $this->defaultTtl;
        $cacheKey = $this->getCacheKey($request);

        // Intentar obtener respuesta de caché
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            return response()->json($cachedResponse['data'], $cachedResponse['status'])
                ->withHeaders($cachedResponse['headers'] ?? [])
                ->header('X-Cache', 'HIT');
        }

        // Ejecutar request
        $response = $next($request);

        // Solo cachear respuestas exitosas
        if ($response->isSuccessful()) {
            Cache::put($cacheKey, [
                'data' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode(),
                'headers' => [
                    'Content-Type' => $response->headers->get('Content-Type'),
                ],
            ], $ttl);

            $response->header('X-Cache', 'MISS');
        }

        return $response;
    }

    /**
     * Generar clave de caché única para el request
     */
    protected function getCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        $body = $request->getContent();

        return 'response:' . md5($url . $body);
    }
}
