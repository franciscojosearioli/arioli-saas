<?php

namespace App\Http\Resources;

use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Pago */
class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'monto' => $this->monto,
            'fecha' => $this->fecha,
            'medio_pago' => $this->medio_pago,
            'notas' => $this->notas,
            'registrado_por' => $this->whenLoaded('registradoPor', fn () => $this->registradoPor ? [
                'id' => $this->registradoPor->id,
                'name' => $this->registradoPor->name,
            ] : null),
            'cuota' => new CuotaResource($this->whenLoaded('cuota')),
            'created_at' => $this->created_at,
        ];
    }
}
