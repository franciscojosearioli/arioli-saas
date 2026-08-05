<?php

namespace App\Http\Resources;

use App\Models\PublicacionCanal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PublicacionCanal */
class PublicacionCanalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'canal' => $this->canal,
            'estado' => $this->estado,
            'external_id' => $this->external_id,
            'programada_para' => $this->programada_para,
            'fecha_publicada' => $this->fecha_publicada,
            'intentos' => $this->intentos,
            'ultimo_error' => $this->ultimo_error,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
