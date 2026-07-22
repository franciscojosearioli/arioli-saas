<?php

namespace App\Console\Commands;

use App\Models\DemoInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Etapa 6.3.2 (ver docs/ARQUITECTURA_MODULAR.md): la otra mitad
 * automática. Busca `DemoInstance` en `expirada` que ya cumplieron el
 * período de gracia (6 horas desde `expirada_at`, decidido junto con
 * Francisco) y delega el borrado físico a `demo:limpiar {id}` — el único
 * comando que toca la conexión mysql_tenant_admin del Gate G-01. No
 * reimplementa el DROP DATABASE ni el manejo de errores: si
 * demo:limpiar deja algo en 'error', acá solo se registra, no se
 * reintenta ni se fuerza nada.
 *
 * Programado cada hora, igual que demo:expirar-vencidas — mismo
 * scheduler, misma cadencia, sin necesidad real (todavía) de una
 * frecuencia distinta para esta mitad del ciclo.
 */
class DemoLimpiarVencidas extends Command
{
    private const PERIODO_GRACIA_HORAS = 6;

    protected $signature = 'demo:limpiar-vencidas';

    protected $description = 'Busca DemoInstance expiradas que ya cumplieron el período de gracia y las limpia una por una (Etapa 6.3.2)';

    public function handle(): int
    {
        $vencidas = DemoInstance::where('status', 'expirada')
            ->where('expirada_at', '<=', now()->subHours(self::PERIODO_GRACIA_HORAS))
            ->get();

        if ($vencidas->isEmpty()) {
            Log::channel('demo-lifecycle')->info('demo:limpiar-vencidas: nada para procesar.');
            return self::SUCCESS;
        }

        foreach ($vencidas as $demo) {
            $exitCode = Artisan::call('demo:limpiar', ['id' => $demo->id]);

            Log::channel('demo-lifecycle')->info('demo:limpiar-vencidas: procesada', [
                'demo_instance_id' => $demo->id,
                'tenant_key' => $demo->tenant_key,
                'expirada_at' => optional($demo->expirada_at)->toDateTimeString(),
                'exit_code' => $exitCode,
                'resultado' => $exitCode === self::SUCCESS ? 'eliminada' : 'fallo (ver status/error_message en demo_instances)',
            ]);
        }

        return self::SUCCESS;
    }
}
