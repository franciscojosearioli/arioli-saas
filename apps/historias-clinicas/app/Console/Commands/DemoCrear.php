<?php

namespace App\Console\Commands;

use App\Models\DemoInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Etapa 6.3.1 (ver docs/ARQUITECTURA_MODULAR.md) — CASO MANUAL, no
 * automatizado: un humano ejecuta este comando a mano para levantar una
 * demo. La automatización (scheduler eligiendo cuándo/qué perfil) es
 * Etapa 6.3.2, todavía no construida a propósito — primero se valida que
 * el ciclo de vida completo funciona antes de programarlo.
 *
 * Reutiliza tenants:crear (6.1) tal cual — demo_instances no duplica nada
 * de tenants, solo agrega el estado explícito y expires_at que tenants no
 * tiene motivo para conocer.
 */
class DemoCrear extends Command
{
    protected $signature = 'demo:crear
        {perfil : Clave de un Perfil en config/platform/perfiles.php}
        {--horas=24 : Horas hasta expirar, desde que queda activa}';

    protected $description = 'Provisiona una DemoInstance nueva (manual, Etapa 6.3.1)';

    public function handle(): int
    {
        $perfilKey = $this->argument('perfil');

        if (! config('perfiles.' . $perfilKey)) {
            $this->error("Perfil '{$perfilKey}' no existe en config/platform/perfiles.php.");
            return self::FAILURE;
        }

        $tenantKey = 'demo_' . $perfilKey . '_' . Str::lower(Str::random(6));

        $demo = DemoInstance::create([
            'tenant_key' => $tenantKey,
            'perfil_key' => $perfilKey,
            'status' => 'pendiente',
        ]);

        $demo->update(['status' => 'provisionando']);

        $exitCode = Artisan::call('tenants:crear', [
            'key' => $tenantKey,
            '--perfil' => $perfilKey,
            '--con-datos-demo' => true,
        ]);
        $this->line(Artisan::output());

        if ($exitCode !== self::SUCCESS) {
            $demo->update([
                'status' => 'error',
                'error_message' => 'tenants:crear terminó con código ' . $exitCode . '. Ver salida arriba.',
            ]);
            $this->error("DemoInstance #{$demo->id} quedó en 'error' — requiere revisión manual, no se reintenta sola.");

            return self::FAILURE;
        }

        $demo->update([
            'status' => 'activa',
            'activada_at' => now(),
            'expires_at' => now()->addHours((int) $this->option('horas')),
        ]);

        $this->info("DemoInstance #{$demo->id} activa: tenant '{$tenantKey}', expira " . $demo->expires_at->toDateTimeString() . '.');

        return self::SUCCESS;
    }
}
