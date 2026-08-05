<?php

namespace App\Http\Resources;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Documento */
class DocumentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'archivo' => $this->archivo,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'version' => $this->version,
            'documentable_type' => class_basename($this->documentable_type),
            'documentable_id' => $this->documentable_id,
            'subido_por' => $this->whenLoaded('subidoPor', fn () => $this->subidoPor ? [
                'id' => $this->subidoPor->id,
                'name' => $this->subidoPor->name,
            ] : null),
            'created_at' => $this->created_at,
        ];
    }
}
