<?php

namespace App\Console\Commands;

use App\Models\CuentaConectada;
use App\Services\Publicaciones\MetaGraphClient;
use Illuminate\Console\Command;

/**
 * §09 Fase 4: "un job diario revisa vencimientos próximos y dispara el
 * refresh automático cuando el flujo OAuth del canal lo permite, o marca
 * la cuenta como 'requiere reconexión'... cuando no." El Page Access
 * Token de Meta no tiene un flujo de refresh propio (no es como un
 * refresh_token de OAuth estándar) — la única acción posible acá es
 * detectar y marcar, la reconexión la hace el tenant a mano desde
 * Configuración (misma pantalla que conectó la cuenta la primera vez).
 */
class RevisarVencimientoCuentasConectadas extends Command
{
    protected $signature = 'cuentas-conectadas:revisar-vencimientos';

    protected $description = 'Marca como "requiere reconexión" las cuentas conectadas con token inválido o por vencer';

    public function handle(MetaGraphClient $graph): int
    {
        $revisadas = 0;

        CuentaConectada::query()->where('estado', 'activa')->each(function (CuentaConectada $cuenta) use ($graph, &$revisadas) {
            $revisadas++;

            if (! $graph->tokenValido($cuenta->access_token)) {
                $cuenta->marcarRequiereReconexion('El token ya no es válido — Meta lo rechazó.');

                return;
            }

            if ($cuenta->estaPorVencer()) {
                $cuenta->marcarRequiereReconexion('El token vence pronto y este canal no tiene refresh automático.');
            }
        });

        $this->info("Cuentas revisadas: {$revisadas}");

        return self::SUCCESS;
    }
}
