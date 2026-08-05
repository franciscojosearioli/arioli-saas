<?php

namespace App\Platform\Services;

use App\Mail\BienvenidaClienteMail;
use App\Models\Tenant;
use App\Platform\Contracts\Services\ProvisionClienteServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ProvisionClienteService implements ProvisionClienteServiceContract
{
    public function provisionar(string $tenantKey, string $perfilKey, string $adminNombre, string $adminEmail): Tenant
    {
        $exitCode = Artisan::call('tenants:crear', [
            'key' => $tenantKey,
            '--perfil' => $perfilKey,
        ]);

        $tenant = Tenant::where('tenant_key', $tenantKey)->first();

        if (! $tenant) {
            throw new \RuntimeException("tenants:crear no dejó ningún registro para '{$tenantKey}' (exit={$exitCode}).");
        }

        if ($exitCode !== 0 || $tenant->status !== 'activo') {
            return $tenant;
        }

        $exitCode = Artisan::call('tenants:asegurar-administrador', [
            'tenant_key' => $tenantKey,
            'nombre' => $adminNombre,
            'email' => $adminEmail,
        ]);

        if ($exitCode !== 0) {
            $tenant->update(['status' => 'error', 'last_migration_status' => 'error']);
            return $tenant->fresh();
        }

        $url = $this->generarUrlReclamoCredenciales($tenant);

        Log::info('Etapa 6.5: cliente provisionado, link de reclamo generado', [
            'tenant_key' => $tenantKey,
            'slug' => $tenant->slug,
            'admin_email' => $adminEmail,
            'url' => $url,
        ]);

        try {
            Mail::to($adminEmail)->send(new BienvenidaClienteMail($adminNombre, $url));
        } catch (\Throwable $e) {
            // No se aborta el provisioning por un fallo de email — el
            // tenant ya está activo y el link queda en el log para
            // reenviarlo a mano si hace falta (mismo criterio que
            // demo:crear: un fallo no crítico no destruye lo ya hecho).
            Log::error('Etapa 6.5: falló el envío del email de bienvenida', [
                'tenant_key' => $tenantKey,
                'error' => $e->getMessage(),
            ]);
        }

        return $tenant->fresh();
    }

    /**
     * El link debe apuntar al subdominio del tenant recién creado
     * ({slug}.clinica.arioli.dev), no al dominio central donde corre
     * este servicio — se fuerza la raíz de URL solo para esta firma,
     * sin tocar la configuración global de la app.
     */
    private function generarUrlReclamoCredenciales(Tenant $tenant): string
    {
        $appUrl = config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST);
        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'https';
        $tenantUrl = $scheme . '://' . $tenant->slug . '.' . $host;

        URL::forceRootUrl($tenantUrl);

        try {
            return URL::temporarySignedRoute(
                'onboarding.credenciales.show',
                now()->addDays(7),
                ['slug' => $tenant->slug]
            );
        } finally {
            URL::forceRootUrl($appUrl);
        }
    }
}
