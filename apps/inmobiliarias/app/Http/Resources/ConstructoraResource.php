<?php

namespace App\Http\Resources;

use App\Models\Constructora;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Constructora */
class ConstructoraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'logo' => $this->logo,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'desarrollos_count' => $this->whenCounted('desarrollos'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
