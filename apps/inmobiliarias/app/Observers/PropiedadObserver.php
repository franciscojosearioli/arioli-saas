<?php

namespace App\Observers;

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

        $propiedad->dispararSincronizacion();
    }

    public function deleted(Propiedad $propiedad): void
    {
        $propiedad->dispararSincronizacion('PropiedadArchivada');
    }
}
