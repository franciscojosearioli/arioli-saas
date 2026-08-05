<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Etapa 6.4 (ver docs/ARQUITECTURA_MODULAR.md): resuelve por `slug`
 * (público, DNS-safe) contra la tabla `tenants`, no por adivinanza de
 * existencia de base de datos. `tenant_key` (interno) nunca se expone acá
 * ni en ningún lado público — se usa solo para namespacing interno
 * (cookie de sesión, prefix de caché, ruta de storage) porque es estable
 * para siempre, a diferencia del slug, que podría regenerarse el día de
 * mañana sin que eso deba mover archivos ya guardados.
 *
 * Exigir 4+ partes en el host (antes: 3+) separa la app central
 * ("clinica.arioli.dev", sin subdominio — 3 partes) del contexto de
 * tenant ("{slug}.clinica.arioli.dev" — 4 partes). Antes de este cambio,
 * el dominio central también intentaba resolverse como si "clinica"
 * fuera un tenant.
 */
class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $this->resolveSlug($request);

        if (!$slug) {
            return $next($request);
        }

        $tenant = Tenant::where('slug', $slug)->where('status', 'activo')->first();

        if (!$tenant) {
            $reservado = in_array($slug, config('reserved_slugs', []), true);

            // Etapa 6.6: "demo" es el caso reservado con más tráfico
            // heredado (viejo punto de entrada demo.clinica.arioli.dev,
            // retirado en Etapa 6). En vez de mostrar la pantalla de
            // "subdominio disponible" para una palabra que nunca va a
            // estar disponible, se redirige directo al portal público
            // real (Etapa 6.4) — no debe quedar ningún "demo general".
            if ($slug === 'demo') {
                $root = implode('.', array_slice(explode('.', $request->getHost()), 1));
                return redirect()->to($request->getScheme() . '://' . $root . '/demo', 302);
            }

            $adminHost   = env('SAAS_ADMIN_HOST', 'admin.127.0.0.1.nip.io');
            $landingHost = str_replace('admin.', '', $adminHost);
            $clienteHost = str_replace('admin.', 'cliente.', $adminHost);
            return response()->view('tenant-not-found', [
                'tenantId'    => $slug,
                'landingHost' => $landingHost,
                'clienteHost' => $clienteHost,
                'reservado'   => $reservado,
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate')
              ->header('Pragma', 'no-cache');
        }

        $request->attributes->set('tenant_id', $tenant->tenant_key);
        $request->attributes->set('tenant_slug', $tenant->slug);

        config(['database.connections.mysql.database' => $tenant->database]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        // Aislar sesión por tenant (cookie distinta por tenant)
        config(['session.cookie' => 'historias_' . $tenant->tenant_key . '_session']);

        // Aislar caché por tenant
        config(['cache.prefix' => 'historias_' . $tenant->tenant_key]);

        // Aislar storage por tenant
        config(['filesystems.disks.local.root' => storage_path('app/tenants/' . $tenant->tenant_key)]);

        return $next($request);
    }

    private function resolveSlug(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Necesita subdominio real: slug.producto.dominio.tld
        // demo-odontologia-a8f29c.clinica.arioli.dev → 4 partes
        // clinica.arioli.dev (app central, sin subdominio) → 3 partes, no resuelve
        if (count($parts) < 4) {
            return null;
        }

        $slug = $parts[0];

        if (!preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', $slug)) {
            return null;
        }

        return $slug;
    }
}