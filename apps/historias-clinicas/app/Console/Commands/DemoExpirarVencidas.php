<?php

namespace App\Console\Commands;

use App\Models\DemoInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Etapa 6.3.2 (ver docs/ARQUITECTURA_MODULAR.md): la mitad "automática" del
 * ciclo validado a mano en 6.3.1. Deliberadamente NO reimplementa nada —
 * solo encuentra qué `DemoInstance` están `activa` con `expires_at`
 * vencido y le delega la transición a `demo:expirar {id}`, el mismo
 * comando ya probado uno por uno. Ninguna lógica de negocio nueva vive
 * acá, solo selección + orquestación + auditoría.
 *
 * Programado cada hora (Kernel::schedule()) — suficiente margen para un
 * objeto que vive del orden de horas, no de minutos.
 */
class DemoExpirarVencidas extends Command
{
    protected $signature = 'demo:expirar-vencidas';

    protected $description = 'Busca DemoInstance activas con expires_at vencido y las expira una por una (Etapa 6.3.2)';

    public function handle(): int
    {
        $vencidas = DemoInstance::where('status', 'activa')
            ->where('expires_at', '<=', now())
            ->get();

        if ($vencidas->isEmpty()) {
            Log::channel('demo-lifecycle')->info('demo:expirar-vencidas: nada para procesar.');
            return self::SUCCESS;
        }

        foreach ($vencidas as $demo) {
            $exitCode = Artisan::call('demo:expirar', ['id' => $demo->id]);

            Log::channel('demo-lifecycle')->info('demo:expirar-vencidas: procesada', [
                'demo_instance_id' => $demo->id,
                'tenant_key' => $demo->tenant_key,
                'expires_at' => optional($demo->expires_at)->toDateTimeString(),
                'exit_code' => $exitCode,
                'resultado' => $exitCode === self::SUCCESS ? 'expirada' : 'fallo',
            ]);
        }

        return self::SUCCESS;
    }
}
