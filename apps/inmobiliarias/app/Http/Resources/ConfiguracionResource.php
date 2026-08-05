<?php

namespace App\Http\Resources;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Configuracion */
class ConfiguracionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'comision_porcentaje' => $this->comision_porcentaje,
            'updated_at' => $this->updated_at,
        ];
    }
}
