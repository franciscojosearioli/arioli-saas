<?php

namespace App\Platform\Services;

use App\Models\DemoInstance;
use App\Platform\Contracts\Services\ProvisionDemoServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ProvisionDemoService implements ProvisionDemoServiceContract
{
    public function provisionar(
        string $perfilKey,
        ?string $solicitanteNombre = null,
        ?string $solicitanteEmail = null,
        int $horasVigencia = 24
    ): DemoInstance {
        if (! config('perfiles.' . $perfilKey)) {
            throw new InvalidArgumentException("Perfil '{$perfilKey}' no existe en config/platform/perfiles.php.");
        }

        $tenantKey = 'demo_' . $perfilKey . '_' . Str::lower(Str::random(6));
        $slug = str_replace('_', '-', $tenantKey);

        $demo = DemoInstance::create([
            'tenant_key' => $tenantKey,
            'perfil_key' => $perfilKey,
            'solicitante_nombre' => $solicitanteNombre,
            'solicitante_email' => $solicitanteEmail,
            'status' => 'pendiente',
        ]);

        $demo->update(['status' => 'provisionando']);

        $exitCode = Artisan::call('tenants:crear', [
            'key' => $tenantKey,
            '--slug' => $slug,
            '--perfil' => $perfilKey,
            '--con-datos-demo' => true,
        ]);

        if ($exitCode !== 0) {
            $demo->update([
                'status' => 'error',
                'error_message' => 'tenants:crear terminó con código ' . $exitCode . '. Ver storage/logs/laravel.log para más detalle.',
            ]);

            return $demo;
        }

        $demo->update([
            'status' => 'activa',
            'activada_at' => now(),
            'expires_at' => now()->addHours($horasVigencia),
        ]);

        return $demo;
    }
}
