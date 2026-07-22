<?php

namespace App\Console\Commands;

use App\Models\DemoInstance;
use Illuminate\Console\Command;

/**
 * Etapa 6.3.1 — transición manual `activa` → `expirada`. No toca MySQL a
 * nivel de tenant (solo un flag en demo_instances, en la DB maestra), por
 * eso no pasa por la conexión mysql_tenant_admin del Gate G-01 — el
 * riesgo de ese usuario acotado aparece recién en demo:limpiar, que sí
 * borra la base física.
 *
 * En 6.3.2 esta transición la hará el scheduler comparando expires_at
 * contra now(); acá se dispara a mano para validar el resto del ciclo
 * sin depender todavía de un cron.
 */
class DemoExpirar extends Command
{
    protected $signature = 'demo:expirar {id : ID de la DemoInstance}';

    protected $description = 'Marca una DemoInstance activa como expirada (manual, Etapa 6.3.1)';

    public function handle(): int
    {
        $demo = DemoInstance::find($this->argument('id'));

        if (! $demo) {
            $this->error('No existe una DemoInstance con ese ID.');
            return self::FAILURE;
        }

        if ($demo->status !== 'activa') {
            $this->error("DemoInstance #{$demo->id} está en estado '{$demo->status}', no 'activa' — no se fuerza la transición.");
            return self::FAILURE;
        }

        $demo->update(['status' => 'expirada']);
        $this->info("DemoInstance #{$demo->id} ('{$demo->tenant_key}') marcada como expirada.");

        return self::SUCCESS;
    }
}
