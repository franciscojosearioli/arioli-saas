<?php

namespace App\Console\Commands;

use App\Models\OutboxEvent;
use App\Models\Propiedad;
use App\Models\PublicacionCanal;
use App\Services\Publicaciones\ChannelAdapterRegistry;
use App\Services\Publicaciones\ContenidoPublicacion;
use Illuminate\Console\Command;
use Throwable;

// §09: el worker del diagrama de sincronización — hace polling de
// outbox_events y llama a adapter->publish()/update() por cada
// PublicacionCanal activo de la Propiedad afectada. Corre vía scheduler
// (bootstrap/app.php), no un listener en cola — mismo criterio simple
// que horizon:snapshot hasta que el volumen justifique una queue dedicada.
class SincronizarPublicaciones extends Command
{
    protected $signature = 'publicaciones:sincronizar';

    protected $description = 'Procesa outbox_events pendientes y sincroniza cada PublicacionCanal activo';

    public function handle(ChannelAdapterRegistry $adapters): int
    {
        $procesados = 0;

        OutboxEvent::query()->pendientes()->orderBy('id')->chunkById(50, function ($eventos) use ($adapters, &$procesados) {
            foreach ($eventos as $evento) {
                $this->procesar($evento, $adapters);
                $procesados++;
            }
        });

        $this->info("Eventos procesados: {$procesados}");

        return self::SUCCESS;
    }

    private function procesar(OutboxEvent $evento, ChannelAdapterRegistry $adapters): void
    {
        $propiedad = Propiedad::find($evento->aggregate_id);

        if (! $propiedad || ! $propiedad->publicacion) {
            $evento->marcarProcesado();

            return;
        }

        $contenido = ContenidoPublicacion::fromPropiedad($propiedad);

        foreach ($propiedad->publicacion->canales as $canal) {
            if (in_array($canal->estado, ['pausada', 'despublicada'], true)) {
                // Pausada/despublicada con external_id todavía activo del
                // lado del canal: hay que decirle que lo retire, no
                // simplemente ignorarlo — si nunca se llegó a publicar
                // (sin external_id), no hay nada que retirar.
                $this->retirarSiHaceFalta($canal, $adapters);

                continue;
            }

            $this->sincronizarCanal($canal, $contenido, $adapters);
        }

        $evento->marcarProcesado();
    }

    private function sincronizarCanal(PublicacionCanal $canal, ContenidoPublicacion $contenido, ChannelAdapterRegistry $adapters): void
    {
        try {
            $adapter = $adapters->para($canal->canal);
            $canal->marcarPublicando();

            if ($canal->external_id) {
                $adapter->update($canal, $contenido);
                $canal->marcarPublicada($canal->external_id);
            } else {
                $canal->marcarPublicada($adapter->publish($canal, $contenido));
            }
        } catch (Throwable $e) {
            $canal->marcarError($e->getMessage());
        }
    }

    private function retirarSiHaceFalta(PublicacionCanal $canal, ChannelAdapterRegistry $adapters): void
    {
        if (! $canal->external_id) {
            return;
        }

        try {
            $adapters->para($canal->canal)->unpublish($canal);
        } catch (Throwable $e) {
            $canal->marcarError($e->getMessage());
        }
    }
}
