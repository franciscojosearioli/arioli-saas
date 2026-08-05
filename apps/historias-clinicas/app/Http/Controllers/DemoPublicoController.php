<?php

namespace App\Http\Controllers;

use App\Models\DemoInstance;
use App\Models\Tenant;
use App\Platform\Contracts\Services\ProvisionDemoServiceContract;
use Illuminate\Http\Request;

/**
 * Etapa 6.4 (ver docs/ARQUITECTURA_MODULAR.md): flujo público de
 * autoservicio para probar el sistema — elegir perfil, dejar nombre/email,
 * y recibir una demo lista para usar. Solo alcanzable desde el dominio
 * central (sin subdominio de tenant resuelto) — IdentifyTenant ya
 * garantiza que un request con tenant resuelto nunca llega a este
 * controller con el contexto de otro tenant activo, pero se valida acá
 * también de forma explícita, por las dudas.
 *
 * Provisioning síncrono, a propósito: no hay worker de colas en esta app
 * (ver Etapa 6.3.2) y el ciclo completo tarda unos segundos — agregar
 * infraestructura de colas solo para evitar esa espera sería anticiparse
 * sin necesidad real.
 */
class DemoPublicoController extends Controller
{
    public function __construct(private ProvisionDemoServiceContract $provisionDemoService)
    {
    }

    public function index(Request $request)
    {
        $this->abortSiHayTenant($request);

        return view('demo-publico.index', [
            'perfiles' => config('perfiles', []),
        ]);
    }

    public function solicitar(Request $request, string $perfilKey)
    {
        $this->abortSiHayTenant($request);

        $perfil = config('perfiles.' . $perfilKey);

        if (! $perfil) {
            abort(404);
        }

        return view('demo-publico.solicitar', [
            'perfilKey' => $perfilKey,
            'perfil' => $perfil,
        ]);
    }

    public function crear(Request $request, string $perfilKey)
    {
        $this->abortSiHayTenant($request);

        $perfil = config('perfiles.' . $perfilKey);

        if (! $perfil) {
            abort(404);
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190'],
        ]);

        $demo = $this->provisionDemoService->provisionar(
            $perfilKey,
            $datos['nombre'],
            $datos['email'],
        );

        if ($demo->status === 'error') {
            return back()
                ->withInput()
                ->with('demo_error', 'No pudimos preparar tu demo en este momento. Por favor, intentá de nuevo en unos minutos.');
        }

        $tenant = Tenant::where('tenant_key', $demo->tenant_key)->first();

        return redirect()->route('demo.publico.listo', ['slug' => $tenant->slug]);
    }

    public function listo(Request $request, string $slug)
    {
        $this->abortSiHayTenant($request);

        // La tabla `tenants` vive solo en la DB maestra — como este
        // controller nunca pasa por IdentifyTenant (no hay subdominio de
        // tenant en este request), la conexión `mysql` sigue apuntando a
        // la maestra, así que esta consulta ya cae donde tiene que caer.
        $tenant = Tenant::where('slug', $slug)->first();

        if (! $tenant) {
            abort(404);
        }

        $demo = DemoInstance::where('tenant_key', $tenant->tenant_key)->first();

        return view('demo-publico.listo', [
            'tenant' => $tenant,
            'demo' => $demo,
            // Este request nunca tuvo subdominio de tenant (abortSiHayTenant
            // ya lo garantiza), así que getHost() ya es el dominio central
            // (ej. clinica.arioli.dev) — se antepone el slug tal cual.
            'url' => 'https://' . $slug . '.' . $request->getHost(),
        ]);
    }

    private function abortSiHayTenant(Request $request): void
    {
        if ($request->attributes->get('tenant_id')) {
            abort(404);
        }
    }
}
