<?php

namespace App\Http\Resources;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Publicacion */
class PublicacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'destacada' => $this->destacada,
            'destacada_hasta' => $this->destacada_hasta,
            'propiedad' => new PropiedadResource($this->whenLoaded('propiedad')),
            'canales' => PublicacionCanalResource::collection($this->whenLoaded('canales')),
            'created_at' => $this->created_at,
        ];
    }
}
