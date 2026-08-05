<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncTenantProfileRequest;
use App\Models\PerfilInmobiliaria;
use Illuminate\Http\JsonResponse;

class TenantProfileController extends Controller
{
    public function update(SyncTenantProfileRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $perfil = PerfilInmobiliaria::sincronizar(
            $datos['tenant_id'],
            collect($datos)->except('tenant_id')->all(),
        );

        return response()->json(['id' => (string) $perfil->id, 'slug' => $perfil->slug]);
    }
}
