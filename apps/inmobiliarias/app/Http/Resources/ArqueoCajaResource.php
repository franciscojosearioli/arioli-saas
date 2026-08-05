<?php

namespace App\Http\Resources;

use App\Models\ArqueoCaja;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ArqueoCaja */
class ArqueoCajaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'monto_esperado' => $this->monto_esperado,
            'monto_contado' => $this->monto_contado,
            'diferencia' => $this->diferencia(),
            'notas' => $this->notas,
            'cerrado_por' => $this->whenLoaded('cerradoPor', fn () => $this->cerradoPor ? [
                'id' => $this->cerradoPor->id,
                'name' => $this->cerradoPor->name,
            ] : null),
            'created_at' => $this->created_at,
        ];
    }
}
