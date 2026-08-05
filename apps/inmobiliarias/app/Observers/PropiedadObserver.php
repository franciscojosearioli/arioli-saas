<?php

namespace App\Observers;

use App\Models\OutboxEvent;
use App\Models\Propiedad;

// §09: "cuando cambia el precio, el estado (vendida/alquilada), una foto
// o la descripción de una Propiedad, el evento de dominio se persiste en
// outbox_events DENTRO DE LA MISMA TRANSACCIÓN que el cambio de negocio."
// Solo importa para una Propiedad que ya tiene Publicación — sin eso no
// hay ningún PublicacionCanal escuchando este cambio.
class PropiedadObserver
{
    private const CAMPOS_PUBLICABLES = ['precio', 'moneda', 'estado', 'descripcion', 'titulo'];

    public function created(Propiedad $propiedad): void
    {
        // Sin Publicación todavía (recién creada) — nada que sincronizar.
    }

    public function updated(Propiedad $propiedad): void
    {
        if (! $propiedad->wasChanged(self::CAMPOS_PUBLICABLES)) {
            return;
        }

        if (! $propiedad->publicacion()->exists()) {
            return;
        }

        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadActualizada',
            'payload' => ['propiedad_id' => $propiedad->id],
        ]);
    }

    public function deleted(Propiedad $propiedad): void
    {
        if (! $propiedad->publicacion()->exists()) {
            return;
        }

        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadArchivada',
            'payload' => ['propiedad_id' => $propiedad->id],
        ]);
    }
}
