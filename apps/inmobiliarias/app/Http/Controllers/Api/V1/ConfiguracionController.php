<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConfiguracionRequest;
use App\Http\Resources\ConfiguracionResource;
use App\Models\Configuracion;
use App\Services\Marketplace\PerfilInmobiliariaSync;

class ConfiguracionController extends Controller
{
    public function show(): ConfiguracionResource
    {
        $configuracion = Configuracion::actual();
        $this->authorize('view', $configuracion);

        return new ConfiguracionResource($configuracion);
    }

    public function update(UpdateConfiguracionRequest $request): ConfiguracionResource
    {
        $configuracion = Configuracion::actual();
        $configuracion->update($request->validated());

        PerfilInmobiliariaSync::sincronizar($configuracion);

        return new ConfiguracionResource($configuracion);
    }
}
