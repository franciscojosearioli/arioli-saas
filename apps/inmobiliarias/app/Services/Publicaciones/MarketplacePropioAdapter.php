<?php

namespace App\Services\Publicaciones;

use App\Models\PublicacionCanal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * §08/§09: el marketplace propio es, desde Publicaciones, el primer
 * canal — el único con acceso vía outbox interno en vez de una API
 * externa de terceros, pero sigue siendo una llamada HTTP a un servicio
 * separado (§01: "nunca acceso directo cross-DB"), nunca una escritura
 * directa a la base del marketplace.
 */
class MarketplacePropioAdapter implements ChannelAdapter
{
    public function publish(PublicacionCanal $canal, ContenidoPublicacion $contenido): string
    {
        $respuesta = $this->cliente()->post('/api/publications', [
            'tenant_id' => tenant('id'),
            'propiedad_id' => $canal->publicacion->propiedad_id,
            ...$this->payload($contenido),
        ]);

        $respuesta->throw();

        return $respuesta->json('id') ?? throw new RuntimeException(
            'El marketplace no devolvió un id de publicación.'
        );
    }

    public function update(PublicacionCanal $canal, ContenidoPublicacion $contenido): void
    {
        $this->cliente()
            ->put("/api/publications/{$canal->external_id}", $this->payload($contenido))
            ->throw();
    }

    public function unpublish(PublicacionCanal $canal): void
    {
        $this->cliente()->delete("/api/publications/{$canal->external_id}")->throw();
    }

    private function cliente()
    {
        $url = config('marketplace.api_url');
        if (! $url) {
            throw new RuntimeException('MARKETPLACE_API_URL no está configurado.');
        }

        return Http::baseUrl($url)
            ->withToken(config('marketplace.api_token'))
            ->timeout(10);
    }

    private function payload(ContenidoPublicacion $contenido): array
    {
        return [
            'titulo' => $contenido->titulo,
            'descripcion' => $contenido->descripcion,
            'precio' => $contenido->precio,
            'moneda' => $contenido->moneda,
            'tipo_operacion' => $contenido->tipoOperacion,
            'tipo_propiedad' => $contenido->tipoPropiedad,
            'estado' => $contenido->estado,
            'direccion' => $contenido->direccion,
            'ciudad' => $contenido->ciudad,
            'provincia' => $contenido->provincia,
            'barrio' => $contenido->barrio,
            'superficie_cubierta' => $contenido->superficieCubierta,
            'superficie_total' => $contenido->superficieTotal,
            'ambientes' => $contenido->ambientes,
            'dormitorios' => $contenido->dormitorios,
            'banos' => $contenido->banos,
            'cocheras' => $contenido->cocheras,
            'servicios' => $contenido->servicios,
            'caracteristicas_destacadas' => $contenido->caracteristicasDestacadas,
            'nombre_desarrollo' => $contenido->nombreDesarrollo,
            'galeria' => $contenido->galeria,
            'slug' => Str::slug($contenido->titulo),
        ];
    }
}
