<?php

namespace App\Services\Publicaciones;

use App\Models\PublicacionCanal;

/**
 * §09: contrato único entre Publicación y cualquier canal — Facebook,
 * Instagram o un canal futuro no conocen a Propiedad, y Propiedad no
 * conoce al canal. Agregar un canal nuevo es escribir una clase que
 * implementa esto y registrarla en ChannelAdapterRegistry; nada en
 * Publicaciones ni en el resto del dominio cambia.
 */
interface ChannelAdapter
{
    /**
     * Publica por primera vez. Devuelve el external_id que la plataforma
     * del canal asigna, para que las siguientes llamadas actualicen en
     * vez de duplicar.
     */
    public function publish(PublicacionCanal $canal, ContenidoPublicacion $contenido): string;

    public function update(PublicacionCanal $canal, ContenidoPublicacion $contenido): void;

    public function unpublish(PublicacionCanal $canal): void;
}
