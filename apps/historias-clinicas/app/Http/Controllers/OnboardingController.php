<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Platform\Contracts\Services\CompleteTenantProvisioningContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md): pantalla pública firmada
 * donde un cliente real define su propia contraseña — mismo principio de
 * confianza que HostingCredentialController en la app central (link de
 * un solo uso, la contraseña nunca la conoce ni la transmite nadie más
 * que el propio cliente). A diferencia de /demo (Etapa 6.4), estas rutas
 * requieren que SÍ haya un tenant resuelto — el reclamo no tiene sentido
 * sin un tenant real detrás.
 */
class OnboardingController extends Controller
{
    public function __construct(private CompleteTenantProvisioningContract $completeTenantProvisioning)
    {
    }

    public function show(Request $request, string $slug)
    {
        abort_unless($request->hasValidSignature(), 403);
        $this->abortSiNoCoincideTenant($request, $slug);

        $tenant = Tenant::on('mysql_tenant_admin')->where('slug', $slug)->first();

        if (! $tenant) {
            abort(404);
        }

        if ($tenant->credencial_claimed_at) {
            return view('onboarding.credenciales-reclamadas');
        }

        return view('onboarding.credenciales', ['slug' => $slug]);
    }

    public function claim(Request $request, string $slug)
    {
        abort_unless($request->hasValidSignature(), 403);
        $this->abortSiNoCoincideTenant($request, $slug);

        $datos = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin = $this->completeTenantProvisioning->completar($slug, $datos['password']);

        if (! $admin) {
            return view('onboarding.credenciales-reclamadas');
        }

        // Cliente real: eligió su propia contraseña recién ahora — pedirle
        // el login de nuevo no suma seguridad, solo fricción (a diferencia
        // de /demo, donde deliberadamente no hay auto-login).
        Auth::login($admin);

        return redirect()->route('admin.dashboard.home');
    }

    private function abortSiNoCoincideTenant(Request $request, string $slug): void
    {
        if ($request->attributes->get('tenant_slug') !== $slug) {
            abort(404);
        }
    }
}
