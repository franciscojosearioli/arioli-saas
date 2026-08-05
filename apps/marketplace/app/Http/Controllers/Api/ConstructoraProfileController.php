<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncConstructoraProfileRequest;
use App\Models\PerfilConstructora;
use Illuminate\Http\JsonResponse;

class ConstructoraProfileController extends Controller
{
    public function update(SyncConstructoraProfileRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $perfil = PerfilConstructora::sincronizar(
            $datos['tenant_id'],
            $datos['constructora_id'],
            collect($datos)->except(['tenant_id', 'constructora_id'])->all(),
        );

        return response()->json(['id' => (string) $perfil->id, 'slug' => $perfil->slug]);
    }
}
