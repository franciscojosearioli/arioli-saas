<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracionSistema;
use App\Services\License\LicenseClientInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupComplete
{
    public function __construct(private LicenseClientInterface $license) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return $next($request);
        }

        // Las instancias DEMO nunca ejecutan onboarding.
        try {
            if ($this->license->isDemo()) {
                return $next($request);
            }
        } catch (\Throwable) {
            // Si la validación de licencia falla, no bloquear el acceso
        }

        try {
            $config = ConfiguracionSistema::instancia();
            if (!$config->setup_completed) {
                return redirect()->route('setup.wizard');
            }
        } catch (\Throwable) {
            // Si la tabla no existe aún (migración pendiente), no bloquear
        }

        return $next($request);
    }
}
