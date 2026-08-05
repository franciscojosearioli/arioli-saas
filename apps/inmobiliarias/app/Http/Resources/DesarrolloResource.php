<?php

namespace App\Http\Resources;

use App\Models\Desarrollo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Desarrollo */
class DesarrolloResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'provincia' => $this->provincia,
            'ciudad' => $this->ciudad,
            'barrio' => $this->barrio,
            'plano_maestro' => $this->plano_maestro,
            'constructora' => new ConstructoraResource($this->whenLoaded('constructora')),
            'propiedades_count' => $this->whenCounted('propiedades'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
