<?php

namespace App\Services\Publicaciones;

use App\Models\Configuracion;
use App\Models\PublicacionCanal;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * §09: "Sitio web de la inmobiliaria — API key propia del tenant". Cada
 * tenant tiene su propia URL/API key en Configuracion — no hay una única
 * URL como con el marketplace, así que esta llamada va contra lo que ese
 * tenant en particular configuró.
 */
class SitioWebAdapter implements ChannelAdapter
{
    public function publish(PublicacionCanal $canal, ContenidoPublicacion $contenido): string
    {
        $respuesta = $this->cliente()->post('/api/inmobiliarias/publicaciones', $this->payload($contenido));
        $respuesta->throw();

        return $respuesta->json('id') ?? throw new RuntimeException(
            'El sitio del tenant no devolvió un id de publicación.'
        );
    }

    public function update(PublicacionCanal $canal, ContenidoPublicacion $contenido): void
    {
        $this->cliente()
            ->put("/api/inmobiliarias/publicaciones/{$canal->external_id}", $this->payload($contenido))
            ->throw();
    }

    public function unpublish(PublicacionCanal $canal): void
    {
        $this->cliente()->delete("/api/inmobiliarias/publicaciones/{$canal->external_id}")->throw();
    }

    private function cliente()
    {
        $configuracion = Configuracion::actual();

        if (! $configuracion->sitio_web_url) {
            throw new RuntimeException('Este tenant no configuró la URL de su sitio web todavía.');
        }

        return Http::baseUrl($configuracion->sitio_web_url)
            ->withToken($configuracion->sitio_web_api_key)
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
            'ubicacion' => [
                'direccion' => $contenido->direccion,
                'ciudad' => $contenido->ciudad,
                'provincia' => $contenido->provincia,
                'barrio' => $contenido->barrio,
            ],
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
        ];
    }
}
