<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Platform\Contracts\Services\ProvisionClienteServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md): la única puerta de
 * entrada para que la app central provisione un tenant de cliente real.
 * Historias-clinicas no sabe nada de planes, pagos ni CRM — solo recibe
 * una orden de provisión (perfil + datos del administrador) y devuelve
 * el resultado. Protegido por ValidateApiKey (api.key), mismo mecanismo
 * que ya usan internal.demo.seed/reset.
 */
class ProvisionController extends Controller
{
    public function __construct(private ProvisionClienteServiceContract $provisionClienteService)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_key' => ['required', 'string', 'regex:/^[a-z0-9][a-z0-9_-]*$/', 'max:100'],
            'perfil' => ['required', 'string'],
            'admin_nombre' => ['required', 'string', 'max:150'],
            'admin_email' => ['required', 'email', 'max:190'],
        ]);

        if (! config('perfiles.' . $validated['perfil'])) {
            return response()->json([
                'success' => false,
                'error' => "Perfil '{$validated['perfil']}' no existe en config/platform/perfiles.php.",
            ], 422);
        }

        if (in_array(strtolower($validated['tenant_key']), config('reserved_slugs', []), true)) {
            return response()->json([
                'success' => false,
                'error' => "'{$validated['tenant_key']}' es un subdominio reservado de la plataforma.",
            ], 422);
        }

        $tenant = $this->provisionClienteService->provisionar(
            $validated['tenant_key'],
            $validated['perfil'],
            $validated['admin_nombre'],
            $validated['admin_email'],
        );

        if ($tenant->status !== 'activo') {
            return response()->json([
                'success' => false,
                'error' => 'Provisioning failed',
                'tenant_key' => $tenant->tenant_key,
                'status' => $tenant->status,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'tenant_key' => $tenant->tenant_key,
            'slug' => $tenant->slug,
        ]);
    }
}
