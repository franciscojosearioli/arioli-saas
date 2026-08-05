<?php

namespace App\Http\Resources;

use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Comision */
class ComisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'porcentaje' => $this->porcentaje,
            'monto' => $this->monto,
            'moneda' => $this->moneda,
            'estado' => $this->estado,
            'fecha_liquidacion' => $this->fecha_liquidacion,
            'agente' => $this->whenLoaded('agente', fn () => [
                'id' => $this->agente->id,
                'name' => $this->agente->name,
            ]),
            'operacion' => new OperacionResource($this->whenLoaded('operacion')),
            'created_at' => $this->created_at,
        ];
    }
}
