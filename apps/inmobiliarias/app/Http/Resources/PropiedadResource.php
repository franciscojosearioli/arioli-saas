<?php

namespace App\Http\Resources;

use App\Models\Propiedad;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Propiedad */
class PropiedadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'precio' => $this->precio,
            'moneda' => $this->moneda,
            'superficie_cubierta' => $this->superficie_cubierta,
            'superficie_total' => $this->superficie_total,
            'ambientes' => $this->ambientes,
            'dormitorios' => $this->dormitorios,
            'banos' => $this->banos,
            'cocheras' => $this->cocheras,
            'manzana' => $this->manzana,
            'numero_lote' => $this->numero_lote,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'ciudad' => $this->ciudad,
            'barrio' => $this->barrio,
            'servicios' => $this->servicios,
            'caracteristicas_destacadas' => $this->caracteristicas_destacadas,
            'atributos' => $this->atributos,
            'desarrollo' => new DesarrolloResource($this->whenLoaded('desarrollo')),
            'propietario' => new ClienteResource($this->whenLoaded('propietario')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
