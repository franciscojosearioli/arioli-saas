<?php

namespace App\Http\Resources;

use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cuota */
class CuotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'monto' => $this->monto,
            'moneda' => $this->moneda,
            'estado' => $this->estado,
            'monto_pagado' => $this->montoPagado(),
            'operacion' => new OperacionResource($this->whenLoaded('operacion')),
            'pagos' => PagoResource::collection($this->whenLoaded('pagos')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
