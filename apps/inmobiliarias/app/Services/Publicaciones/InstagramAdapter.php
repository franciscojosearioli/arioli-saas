<?php

namespace App\Services\Publicaciones;

use App\Models\PublicacionCanal;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * §09 Fase 4: publica en la cuenta profesional de Instagram vinculada a
 * la Página conectada — la Graph API de Instagram publica en dos pasos
 * (crear un contenedor, después publicarlo), a diferencia de Facebook.
 */
class InstagramAdapter implements ChannelAdapter
{
    use ConcernsMetaGraphApi;

    public function publish(PublicacionCanal $canal, ContenidoPublicacion $contenido): string
    {
        $cuenta = $this->cuentaConectada('instagram');

        if (empty($contenido->galeria)) {
            throw new RuntimeException('Instagram requiere al menos una foto — esta propiedad no tiene ninguna cargada.');
        }

        $contenedor = Http::post($this->graphUrl("{$cuenta->external_account_id}/media"), [
            'image_url' => $contenido->galeria[0],
            'caption' => $this->armarCaption($contenido),
            'access_token' => $cuenta->access_token,
        ]);
        $contenedor->throw();

        $idContenedor = $contenedor->json('id')
            ?? throw new RuntimeException('Instagram no devolvió un id de contenedor.');

        $publicacion = Http::post($this->graphUrl("{$cuenta->external_account_id}/media_publish"), [
            'creation_id' => $idContenedor,
            'access_token' => $cuenta->access_token,
        ]);
        $publicacion->throw();

        return $publicacion->json('id') ?? throw new RuntimeException('Instagram no devolvió un id de publicación.');
    }

    /**
     * La Graph API de Instagram no permite editar el caption de un post
     * ya publicado — no es una limitación de este adapter, es del canal
     * (mismo criterio que Facebook Marketplace en modo degradado, §09):
     * no falla, no hay nada que este adapter pueda actualizar del lado
     * de Meta.
     */
    public function update(PublicacionCanal $canal, ContenidoPublicacion $contenido): void
    {
        // No-op a propósito — ver el comentario de la clase.
    }

    /**
     * Tampoco existe un endpoint de borrado de media en la Graph API de
     * Instagram — despublicar ahí es una acción manual del tenant desde
     * la app, no algo que este adapter pueda hacer.
     */
    public function unpublish(PublicacionCanal $canal): void
    {
        // No-op a propósito — ver el comentario de update().
    }

    private function armarCaption(ContenidoPublicacion $contenido): string
    {
        // Instagram no soporta links clickeables en el caption — el link
        // a la ficha va en la bio o en un story, fuera de alcance acá.
        $precio = $contenido->precio
            ? "{$contenido->moneda} ".number_format((float) $contenido->precio, 0, ',', '.')
            : null;
        $ubicacion = collect([$contenido->barrio, $contenido->ciudad, $contenido->provincia])->filter()->implode(', ');

        return collect([$contenido->titulo, $precio, $ubicacion])->filter()->implode(' · ');
    }
}
