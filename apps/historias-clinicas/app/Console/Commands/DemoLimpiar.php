<?php

namespace App\Console\Commands;

use App\Models\DemoInstance;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Etapa 6.3.1 (ver docs/ARQUITECTURA_MODULAR.md) — CASO MANUAL: un humano
 * ejecuta este comando a mano para limpiar una demo expirada. Es la única
 * pieza de este ciclo de vida que borra una base física, por eso es la
 * única que usa la conexión mysql_tenant_admin (Gate G-01) — el usuario
 * acotado a `historias_%`, nunca saas_user.
 *
 * Mismo criterio que tenants:crear: ante cualquier falla, el registro
 * queda en 'error' con el mensaje, para que un humano decida — no hay
 * reintento automático ni rollback a ciegas.
 */
class DemoLimpiar extends Command
{
    protected $signature = 'demo:limpiar {id : ID de la DemoInstance}';

    protected $description = 'Elimina físicamente una DemoInstance expirada: DB + registro tenants (manual, Etapa 6.3.1)';

    public function handle(): int
    {
        $demo = DemoInstance::find($this->argument('id'));

        if (! $demo) {
            $this->error('No existe una DemoInstance con ese ID.');
            return self::FAILURE;
        }

        if ($demo->status !== 'expirada') {
            $this->error("DemoInstance #{$demo->id} está en estado '{$demo->status}', no 'expirada' — no se limpia.");
            return self::FAILURE;
        }

        $tenant = Tenant::where('tenant_key', $demo->tenant_key)->first();

        if (! $tenant) {
            $demo->update([
                'status' => 'error',
                'error_message' => "No existe un registro en 'tenants' para '{$demo->tenant_key}' — inconsistencia entre demo_instances y tenants, requiere revisión manual.",
            ]);
            $this->error("DemoInstance #{$demo->id}: sin tenant asociado. Marcada 'error', no se toca nada más.");

            return self::FAILURE;
        }

        if (! preg_match('/^historias_[a-z0-9_]+$/', $tenant->database)) {
            $demo->update([
                'status' => 'error',
                'error_message' => "El nombre de base '{$tenant->database}' no matchea el patrón historias_% esperado — abortado por seguridad, requiere revisión manual.",
            ]);
            $this->error("DemoInstance #{$demo->id}: nombre de base fuera de patrón, abortado.");

            return self::FAILURE;
        }

        $demo->update(['status' => 'eliminando']);

        try {
            DB::connection('mysql_tenant_admin')->statement("DROP DATABASE IF EXISTS `{$tenant->database}`");
            $tenant->delete();
        } catch (Throwable $e) {
            $demo->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
            $this->error("Falló la limpieza de DemoInstance #{$demo->id}: {$e->getMessage()}");

            return self::FAILURE;
        }

        $demo->update([
            'status' => 'eliminada',
            'eliminada_at' => now(),
        ]);

        $this->info("DemoInstance #{$demo->id} ('{$demo->tenant_key}') eliminada: base '{$tenant->database}' borrada, registro tenants eliminado.");

        return self::SUCCESS;
    }
}
