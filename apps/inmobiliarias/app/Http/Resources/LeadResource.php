<?php

namespace App\Http\Resources;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lead */
class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'origen' => $this->origen,
            'estado' => $this->estado,
            'interes' => $this->interes,
            'notas' => $this->notas,
            'agente' => $this->whenLoaded('agente', fn () => [
                'id' => $this->agente->id,
                'name' => $this->agente->name,
            ]),
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'propiedad' => new PropiedadResource($this->whenLoaded('propiedad')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
