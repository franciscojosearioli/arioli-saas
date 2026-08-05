<?php

namespace App\Http\Middleware;

use App\Models\DemoInstance;
use App\Services\License\LicenseClientInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoProtection
{
    public function __construct(private LicenseClientInterface $license) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $isDemo = $this->license->isDemo();
        } catch (\Throwable) {
            $isDemo = false;
        }

        // license->isDemo() solo conoce demos registrados centralmente
        // (el demo oficial viejo) — los de autoservicio (Etapa 6.4) no
        // tienen licencia central, así que sin este chequeo quedaban sin
        // ninguna protección de escritura.
        if (! $isDemo) {
            $tenantId = $request->attributes->get('tenant_id');
            $isDemo = $tenantId && DemoInstance::on('mysql_tenant_admin')->where('tenant_key', $tenantId)->exists();
        }

        if (!$isDemo) {
            return $next($request);
        }

        if ($this->isBlockedAction($request)) {
            if ($request->expectsJson()) {
                return response()->json(
                    ['message' => 'Acción no permitida en entorno demo.'], 403
                );
            }
            return back()->with(
                'demo_blocked',
                'Esta acción no está disponible en el entorno de demostración.'
            );
        }

        return $next($request);
    }

    private function isBlockedAction(Request $request): bool
    {
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return false;
        }

        $routeName = $request->route()?->getName();
        if (in_array($routeName, config('demo.blocked_routes', []))) {
            return true;
        }

        $protectedId = (int) config('demo.protected_user_id', 1);
        if ($protectedId > 0 && (int) $request->route('user') === $protectedId) {
            return true;
        }

        return false;
    }
}
