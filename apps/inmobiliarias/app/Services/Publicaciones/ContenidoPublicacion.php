<?php

namespace App\Services\Publicaciones;

use App\Models\Propiedad;

/**
 * §09: la ficha base se arma UNA vez acá, desde los datos ya cargados de
 * la Propiedad — cada ChannelAdapter la ADAPTA a los límites de su
 * plataforma, nunca inventa contenido propio.
 *
 * Campos que el diseño (§09) contempla pero todavía no tienen una fuente
 * real en el dominio (branding/contacto del tenant, video, plano de la
 * propiedad en sí — el plano_maestro es del Desarrollo) quedan afuera a
 * propósito en vez de simularse con datos falsos; se agregan cuando el
 * módulo de Configuración/branding exista.
 */
final readonly class ContenidoPublicacion
{
    public function __construct(
        public string $titulo,
        public ?string $descripcion,
        public ?string $precio,
        public string $moneda,
        public ?string $tipoOperacion,
        public string $tipoPropiedad,
        public string $estado,
        public ?string $direccion,
        public ?string $ciudad,
        public ?string $provincia,
        public ?string $barrio,
        public ?string $superficieCubierta,
        public ?string $superficieTotal,
        public ?int $ambientes,
        public ?int $dormitorios,
        public ?int $banos,
        public ?int $cocheras,
        public array $servicios,
        public array $caracteristicasDestacadas,
        public ?string $nombreDesarrollo,
        public array $galeria,
    ) {}

    public static function fromPropiedad(Propiedad $propiedad): self
    {
        $operacionActiva = $propiedad->operaciones()
            ->where('estado', 'abierta')
            ->latest('fecha_inicio')
            ->first();

        return new self(
            titulo: $propiedad->titulo,
            descripcion: $propiedad->descripcion,
            precio: $propiedad->precio,
            moneda: $propiedad->moneda,
            tipoOperacion: $operacionActiva?->tipo,
            tipoPropiedad: $propiedad->tipo,
            estado: $propiedad->estado,
            direccion: $propiedad->direccion,
            ciudad: $propiedad->ciudad,
            provincia: $propiedad->provincia,
            barrio: $propiedad->barrio,
            superficieCubierta: $propiedad->superficie_cubierta,
            superficieTotal: $propiedad->superficie_total,
            ambientes: $propiedad->ambientes,
            dormitorios: $propiedad->dormitorios,
            banos: $propiedad->banos,
            cocheras: $propiedad->cocheras,
            servicios: $propiedad->servicios ?? [],
            caracteristicasDestacadas: $propiedad->caracteristicas_destacadas ?? [],
            nombreDesarrollo: $propiedad->desarrollo?->nombre,
            galeria: $propiedad->fotos->pluck('url')->all(),
        );
    }
}
