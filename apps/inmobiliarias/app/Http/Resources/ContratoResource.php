<?php

namespace App\Http\Resources;

use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Contrato */
class ContratoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'clausulas' => $this->clausulas,
            'notas' => $this->notas,
            'renueva_contrato_id' => $this->renueva_contrato_id,
            'operacion' => new OperacionResource($this->whenLoaded('operacion')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
