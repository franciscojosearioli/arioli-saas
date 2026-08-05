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
            'sitio_web_url' => $this->sitio_web_url,
            'sitio_web_api_key' => $this->sitio_web_api_key,
            'updated_at' => $this->updated_at,
        ];
    }
}
