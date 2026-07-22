<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $this->resolveTenantId($request);

        if (!$tenantId) {
            return $next($request);
        }

        $request->attributes->set('tenant_id', $tenantId);

        $dbName = 'historias_' . $tenantId;

        // Connect to information_schema first so the existence check doesn't
        // depend on the tenant default DB (historias_default) being present.
        config(['database.connections.mysql.database' => 'information_schema']);
        DB::purge('mysql');

        try {
            $exists = DB::select(
                "SELECT SCHEMA_NAME FROM SCHEMATA WHERE SCHEMA_NAME = ?",
                [$dbName]
            );
        } catch (\Throwable) {
            $exists = [];
        }

        if (empty($exists)) {
            config(['database.connections.mysql.database' => env('DB_DATABASE', 'historias_default')]);
            DB::purge('mysql');
            $adminHost   = env('SAAS_ADMIN_HOST', 'admin.127.0.0.1.nip.io');
            $landingHost = str_replace('admin.', '', $adminHost);
            $clienteHost = str_replace('admin.', 'cliente.', $adminHost);
            return response()->view('tenant-not-found', [
                'tenantId'    => $tenantId,
                'landingHost' => $landingHost,
                'clienteHost' => $clienteHost,
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate')
              ->header('Pragma', 'no-cache');
        }

        config(['database.connections.mysql.database' => $dbName]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        // Aislar sesión por tenant (cookie distinta por tenant)
        config(['session.cookie' => 'historias_' . $tenantId . '_session']);

        // Aislar caché por tenant
        config(['cache.prefix' => 'historias_' . $tenantId]);

        // Aislar storage por tenant
        config(['filesystems.disks.local.root' => storage_path('app/tenants/' . $tenantId)]);

        return $next($request);
    }

    private function resolveTenantId(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Necesita al menos: tenant.producto.dominio
        // acme.historias-clinicas.arioli.dev → ['acme', 'historias-clinicas', 'arioli', 'dev']
        // acme.historias-clinicas.127.0.0.1.nip.io → ['acme', 'historias-clinicas', '127', ...]
        if (count($parts) < 3) {
            return null;
        }

        $tenantId = $parts[0];

        // Validar que sea un slug válido (letras, números, guiones)
        if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $tenantId)) {
            return null;
        }

        return $tenantId;
    }
}