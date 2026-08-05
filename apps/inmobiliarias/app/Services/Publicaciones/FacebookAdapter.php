<?php

namespace App\Services\Publicaciones;

use App\Models\PublicacionCanal;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * §09 Fase 4: publica en la Página de Facebook conectada (§04
 * CuentaConectada) vía Graph API. El texto y el link son lo único que
 * Facebook necesita — las fotos quedan fuera de este primer adapter
 * (álbum multi-foto vía Graph API es un endpoint aparte, no una simple
 * variación de este; se suma cuando el negocio lo priorice, no se finge
 * acá con una sola imagen).
 */
class FacebookAdapter implements ChannelAdapter
{
    use ConcernsMetaGraphApi;

    public function publish(PublicacionCanal $canal, ContenidoPublicacion $contenido): string
    {
        $cuenta = $this->cuentaConectada('facebook');

        $respuesta = Http::post($this->graphUrl("{$cuenta->external_account_id}/feed"), [
            'message' => $this->armarTexto($contenido),
            'link' => $contenido->storefrontUrl,
            'access_token' => $cuenta->access_token,
        ]);

        $respuesta->throw();

        return $respuesta->json('id') ?? throw new RuntimeException('Facebook no devolvió un id de post.');
    }

    public function update(PublicacionCanal $canal, ContenidoPublicacion $contenido): void
    {
        $cuenta = $this->cuentaConectada('facebook');

        Http::post($this->graphUrl($canal->external_id), [
            'message' => $this->armarTexto($contenido),
            'access_token' => $cuenta->access_token,
        ])->throw();
    }

    public function unpublish(PublicacionCanal $canal): void
    {
        $cuenta = $this->cuentaConectada('facebook');

        Http::delete($this->graphUrl($canal->external_id), [
            'access_token' => $cuenta->access_token,
        ])->throw();
    }

    private function armarTexto(ContenidoPublicacion $contenido): string
    {
        $precio = $contenido->precio
            ? "{$contenido->moneda} ".number_format((float) $contenido->precio, 0, ',', '.')
            : null;
        $ubicacion = collect([$contenido->barrio, $contenido->ciudad, $contenido->provincia])->filter()->implode(', ');

        return collect([$contenido->titulo, $precio, $ubicacion, $contenido->descripcion])
            ->filter()
            ->implode("\n\n");
    }
}
