<?php

namespace App\Http\Resources;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cliente */
class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_persona' => $this->tipo_persona,
            'nombre' => $this->nombre,
            'documento' => $this->documento,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'ciudad' => $this->ciudad,
            'notas' => $this->notas,
            'tiene_portal' => $this->user_id !== null,
            'propiedades_count' => $this->whenCounted('propiedades'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
