<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ValidateLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        // El tenant lo identifica InitializeTenancyByDomain antes que este
        // middleware (mapeado en routes/tenant.php, no en un atributo
        // custom como en loteos — acá lo da el helper tenant() de Stancl).
        $tenantId = tenant('id');
        $apiUrl = config('saas.api_url');
        $token = config('saas.api_token');

        // Sin configuración o sin tenant identificado, pasar (evita romper dev)
        if (! $tenantId || ! $apiUrl || ! $token) {
            return $next($request);
        }

        $cacheKey = "saas_license_{$tenantId}";

        $result = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($apiUrl, $token, $tenantId) {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->withHeaders(['Host' => config('saas.admin_host')])
                    ->get($apiUrl, [
                        'tenant' => $tenantId,
                        'product' => 'inmobiliarias',
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                // Si la API no responde no bloqueamos (fail-open)
            }

            return ['valid' => true, 'fallback' => true];
        });

        if (! ($result['valid'] ?? false)) {
            return response()->view('errors.license-invalid', [
                'reason' => $result['reason'] ?? 'unknown',
            ], 402);
        }

        return $next($request);
    }
}
