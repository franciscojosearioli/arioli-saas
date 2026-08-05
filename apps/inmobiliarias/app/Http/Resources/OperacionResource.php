<?php

namespace App\Http\Resources;

use App\Models\Operacion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Operacion */
class OperacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'estado' => $this->estado,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_cierre' => $this->fecha_cierre,
            'monto' => $this->monto,
            'moneda' => $this->moneda,
            'indice_actualizacion' => $this->indice_actualizacion,
            'notas' => $this->notas,
            'propiedad' => new PropiedadResource($this->whenLoaded('propiedad')),
            'agente' => $this->whenLoaded('agente', fn () => [
                'id' => $this->agente->id,
                'name' => $this->agente->name,
            ]),
            'partes' => ClienteConRolResource::collection($this->whenLoaded('partes')),
            'comision' => new ComisionResource($this->whenLoaded('comision')),
            'cuotas_count' => $this->whenCounted('cuotas'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
