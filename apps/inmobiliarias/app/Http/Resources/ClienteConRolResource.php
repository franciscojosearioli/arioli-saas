<?php

namespace App\Http\Resources;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cliente */
class ClienteConRolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo_persona' => $this->tipo_persona,
            'rol' => $this->pivot->rol,
        ];
    }
}
