<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncDesarrolloRequest;
use App\Models\Desarrollo;
use Illuminate\Http\JsonResponse;

class DesarrolloController extends Controller
{
    public function update(SyncDesarrolloRequest $request): JsonResponse
    {
        $datos = $request->validated();
        $wkt = $datos['ubicacion_wkt'] ?? null;

        $desarrollo = Desarrollo::sincronizar(
            $datos['tenant_id'],
            $datos['desarrollo_id'],
            collect($datos)->except(['tenant_id', 'desarrollo_id', 'ubicacion_wkt'])->all(),
        );

        $desarrollo->guardarUbicacion($wkt);

        return response()->json(['id' => (string) $desarrollo->id, 'slug' => $desarrollo->slug]);
    }
}
