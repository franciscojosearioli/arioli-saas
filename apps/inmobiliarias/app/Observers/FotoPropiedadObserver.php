<?php

namespace App\Observers;

use App\Models\FotoPropiedad;
use App\Models\OutboxEvent;
use App\Models\Propiedad;

// §09: agregar/quitar una foto también dispara sincronización — la
// galería es parte del contenido publicado.
class FotoPropiedadObserver
{
    public function created(FotoPropiedad $foto): void
    {
        $this->encolar($foto);
    }

    public function deleted(FotoPropiedad $foto): void
    {
        $this->encolar($foto);
    }

    private function encolar(FotoPropiedad $foto): void
    {
        if (! Propiedad::whereKey($foto->propiedad_id)->whereHas('publicacion')->exists()) {
            return;
        }

        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $foto->propiedad_id,
            'evento' => 'PropiedadActualizada',
            'payload' => ['propiedad_id' => $foto->propiedad_id, 'motivo' => 'galeria'],
        ]);
    }
}
