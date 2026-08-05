<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Order;
use App\Models\User;
use App\Services\Clients\ClientServiceHealthService;
use App\Services\Clients\ClientUpcomingRenewalsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    private function getUser()
    {
        return Auth::guard('cliente')->user();
    }

    public function index()
    {
        // Enterprise authorization check - client portal access
        Gate::authorize('client-access');

        $user = $this->getUser();

        if ($user->client_id) {
            return $this->clientPortalDashboard($user);
        }

        $license = License::with('plan.product')
            ->where('tenant_id', $user->tenant_id)
            ->where('active', true)
            ->latest()
            ->first();

        $domain = DB::connection('central')
            ->table('domains')
            ->where('tenant_id', $user->tenant_id)
            ->first();

        $pendingOrders = Order::with('plan.product')
            ->where('customer_email', $user->email)
            ->where('status', 'pending')
            ->whereNotNull('mp_preference_id')
            ->latest()
            ->get();

        $totalLicenses = License::where('tenant_id', $user->tenant_id)->count();
        $totalPagos    = Order::where('customer_email', $user->email)
                            ->where('status', 'approved')->sum('amount');

        // Estadísticas de tickets
        $myTickets       = \App\Models\Ticket::where('tenant_id', $user->tenant_id)->where('user_id', $user->id)->count();
        $openTickets     = \App\Models\Ticket::where('tenant_id', $user->tenant_id)->where('user_id', $user->id)
                            ->whereIn('status', ['abierto', 'en_progreso', 'esperando_cliente'])->count();
        $resolvedTickets = \App\Models\Ticket::where('tenant_id', $user->tenant_id)->where('user_id', $user->id)
                            ->where('status', 'resuelto')->count();
        $recentTickets   = \App\Models\Ticket::where('tenant_id', $user->tenant_id)->where('user_id', $user->id)
                            ->whereDate('created_at', '>=', now()->subDays(7))->count();

        return view('cliente.dashboard',
            compact('user', 'license', 'domain', 'pendingOrders', 'totalLicenses', 'totalPagos',
                   'myTickets', 'openTickets', 'resolvedTickets', 'recentTickets'));
    }

    private function clientPortalDashboard(User $user)
    {
        $client = $user->client()->with([
            'hostings.account', 'domains', 'sslCertificates', 'cloudflareServices',
            'services', 'charges', 'licenses.plan.product', 'licenses.domain',
            'projects.domain', 'projects.hosting.account', 'projects.sslCertificate',
            'projects.cloudflareService', 'projects.license.plan.product', 'projects.license.domain',
        ])->firstOrFail();

        $upcomingRenewals = (new ClientUpcomingRenewalsService())->calculate($client);
        $overallHealth = (new ClientServiceHealthService())->calculate($client)['overall'];

        // Sistemas = proyectos que efectivamente tienen algún activo real
        // vinculado (dominio, hosting, SSL, Cloudflare o licencia) — así el
        // cliente ve de un vistazo si un dominio es "solo hosting" o un
        // sistema con licencia (tenant) atrás, y evita confundir un dominio
        // de un tenant con el de un hosting suelto. De cada uno se deriva
        // además la URL real de acceso (si hay licencia) para poder entrar
        // con un solo clic desde el dashboard.
        $systems = $client->projects->filter(
            fn ($p) => $p->domain || $p->hosting || $p->sslCertificate || $p->cloudflareService || $p->license
        )->values();

        // Hosting/licencias que todavía no están agrupados en ningún sistema
        // (clientes viejos sin Project, o activos cargados sueltos) — se
        // muestran igual como accesos independientes, no se pierden.
        $standaloneHostings = $client->hostings->reject(
            fn ($h) => $systems->contains(fn ($s) => $s->hosting_id === $h->id)
        )->values();

        $standaloneLicenses = $client->licenses->reject(
            fn ($l) => $systems->contains(fn ($s) => $s->license_id === $l->id)
        )->values();

        return view('cliente.dashboard-client', [
            'user'                  => $user,
            'client'                => $client,
            'activeHostingCount'    => $client->hostings->where('status', \App\Enums\HostingStatus::Activo)->count(),
            'domainCount'           => $client->domains->count(),
            'serviceCount'          => $client->services->where('status', \App\Enums\ClientServiceStatus::Active)->count(),
            'pendingChargesCount'   => $client->charges->where('status', \App\Enums\ChargeStatus::Pending)->count(),
            'upcomingRenewals'      => $upcomingRenewals,
            'services'              => $client->services->where('status', \App\Enums\ClientServiceStatus::Active)->values(),
            'overallHealth'         => $overallHealth,
            'systems'               => $systems,
            'standaloneHostings'    => $standaloneHostings,
            'standaloneLicenses'    => $standaloneLicenses,
        ]);
    }

    public function showLicense(License $license)
    {
        Gate::authorize('client-access');

        $user = $this->getUser();
        abort_if($license->tenant_id !== $user->tenant_id, 403);

        $license->load('plan.product');

        $domain = \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $user->tenant_id)->first();

        $accessUser = \App\Models\User::where('tenant_id', $user->tenant_id)->first();

        return view('cliente.licencia', compact('license', 'domain', 'user', 'accessUser'));
    }

    public function setCustomDomain(Request $request, License $license)
    {
        Gate::authorize('client-access');

        $user = $this->getUser();
        abort_if($license->tenant_id !== $user->tenant_id, 403);

        $validated = $request->validate([
            'custom_domain' => 'nullable|string|max:253|regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
        ], [
            'custom_domain.regex' => 'El formato del dominio no es válido (ej: mi-sistema.empresa.com).',
        ]);

        $license->update(['custom_domain' => $validated['custom_domain'] ?: null]);

        if ($validated['custom_domain']) {
            $primaryDomain = \Stancl\Tenancy\Database\Models\Domain::where('tenant_id', $user->tenant_id)->value('domain');
            $msg = "Dominio personalizado guardado. Apuntá tu DNS con un CNAME a {$primaryDomain}.";
        } else {
            $msg = 'Dominio personalizado eliminado.';
        }

        return back()->with('success', $msg);
    }

    public function factoryReset(Request $request, License $license)
    {
        Gate::authorize('client-access');

        $user = $this->getUser();
        abort_if($license->tenant_id !== $user->tenant_id, 403);

        $request->validate([
            'confirm_reset' => 'required|in:RESETEAR',
        ], [
            'confirm_reset.in' => 'Escribí exactamente RESETEAR para confirmar.',
        ]);

        $license->load('plan.product');
        $slug = $license->plan->product->slug ?? null;

        $jobClass = match($slug) {
            'loteos'             => \App\Jobs\ProvisionLoteosInstance::class,
            'historias-clinicas' => \App\Jobs\ProvisionHistoriasInstance::class,
            'tallerpro'          => \App\Jobs\ProvisionTallerProInstance::class,
            default              => null,
        };

        if (!$jobClass) {
            return back()->withErrors(['confirm_reset' => 'No se puede resetear este tipo de sistema.']);
        }

        $product    = $license->plan->product;
        $baseDomain = config('app.tenant_domain');

        $jobClass::dispatch(
            $user->tenant_id,
            $product->slug,
            $product->public_domain . '.' . $baseDomain,
            $user->name,
            $user->email,
            'reset_' . now()->timestamp,
        );

        return back()->with('success', 'Reset de fábrica iniciado. En unos minutos el sistema volverá a su estado inicial. Recibirás una nueva contraseña por email.');
    }

    public function licencias()
    {
        // Enterprise authorization check - client access and license viewing
        Gate::authorize('client-access');
        $this->authorize('viewAny', License::class);

        $user     = $this->getUser();
        $licenses = License::with('plan.product')
            ->where('tenant_id', $user->tenant_id)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $domains = DB::connection('central')
            ->table('domains')
            ->where('tenant_id', $user->tenant_id)
            ->get()
            ->keyBy('license_id');

        return view('cliente.licencias', compact('user', 'licenses', 'domains'));
    }

    public function pagos()
    {
        // Enterprise authorization check - client access and order viewing
        Gate::authorize('client-access');
        $this->authorize('viewAny', Order::class);

        $user   = $this->getUser();
        $orders = Order::with('plan.product')
            ->where('customer_email', $user->email)
            ->latest()
            ->paginate(15);

        $totalGastado = Order::where('customer_email', $user->email)
            ->where('status', 'approved')->sum('amount');

        return view('cliente.pagos', compact('user', 'orders', 'totalGastado'));
    }

    public function perfil()
    {
        // Enterprise authorization check - client access and profile viewing
        Gate::authorize('client-access');

        $user = $this->getUser();
        return view('cliente.perfil', compact('user'));
    }

    public function updatePerfil(Request $request)
    {
        // Enterprise authorization check - client access and profile update
        Gate::authorize('client-access');
        $user = $this->getUser();
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function showRenovar(string $licenseId)
    {
        // Enterprise authorization check - client access and license management
        Gate::authorize('client-access');

        $user    = $this->getUser();
        $license = License::with('plan.product')
            ->where('id', $licenseId)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $this->authorize('view', $license);

        // Una licencia indefinida no vence — no tiene sentido "renovarla".
        if ($license->expires_at === null) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Esta licencia no tiene vencimiento, no hace falta renovarla.');
        }

        $plans = \App\Models\Plan::sellable()
            ->where('product_id', $license->plan->product_id)
            ->orderBy('period_months')
            ->get();

        return view('cliente.renovar', compact('license', 'plans', 'user'));
    }

    public function processRenovar(Request $request, string $licenseId)
    {
        // Enterprise authorization check - client access and license management
        Gate::authorize('client-access');

        $user    = $this->getUser();
        $license = License::with('plan.product')
            ->where('id', $licenseId)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $this->authorize('view', $license);

        if ($license->expires_at === null) {
            return redirect()->route('cliente.dashboard')
                ->with('error', 'Esta licencia no tiene vencimiento, no hace falta renovarla.');
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // sellable() es la protección real acá: un plan Demo nunca puede usarse para
        // renovar una licencia real, sin importar qué id se mande en el POST.
        $plan = \App\Models\Plan::sellable()->find($validated['plan_id']);

        if (! $plan || $plan->product_id !== $license->plan->product_id) {
            return back()->withErrors(['plan_id' => 'Plan inválido.']);
        }

        try {
            \MercadoPago\MercadoPagoConfig::setAccessToken(
                config('mercadopago.mode') === 'production'
                    ? config('mercadopago.access_token_prod')
                    : config('mercadopago.access_token_test')
            );

            $client = new \MercadoPago\Client\Preference\PreferenceClient();

            $order = Order::create([
                'plan_id'          => $plan->id,
                'tenant_id'        => $user->tenant_id,
                'customer_name'    => $user->name,
                'customer_email'   => $user->email,
                'customer_company' => $user->tenant_id,
                'amount'           => $plan->price,
                'status'           => 'pending',
            ]);

            $preference = $client->create([
                'items' => [[
                    'title'       => 'Renovación ' . $license->plan->product->name . ' — ' . $plan->period_label,
                    'quantity'    => 1,
                    'unit_price'  => (float) $plan->price,
                    'currency_id' => 'ARS',
                ]],
                'payer' => [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                'external_reference'   => $order->uuid,
                'statement_descriptor' => 'Arioli.dev',
            ]);

            $order->update(['mp_preference_id' => $preference->id]);

            $url = config('mercadopago.mode') === 'production'
                ? $preference->init_point
                : $preference->sandbox_init_point;

            return redirect($url);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Renovacion MP error: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el pago. Intentá de nuevo.');
        }
    }

    public function baja(string $licenseId)
    {
        // Enterprise authorization check - client access and license deactivation
        Gate::authorize('client-access');

        $user    = $this->getUser();
        $license = License::where('id', $licenseId)
            ->where('tenant_id', $user->tenant_id)
            ->firstOrFail();

        $this->authorize('view', $license);

        $license->update(['active' => false]);

        $license->load('plan.product');
        $tenantName = auth()->guard('cliente')->user()->name;
        \App\Support\NotificationHelper::licenceCancelled($license, $tenantName);

        return redirect()->route('cliente.licencias')
            ->with('success', 'Licencia dada de baja correctamente.');
    }

    public function cancelOrder(string $orderId)
    {
        // Enterprise authorization check - client access and order cancellation
        Gate::authorize('client-access');

        $user  = $this->getUser();
        $order = Order::where('id', $orderId)
            ->where('customer_email', $user->email)
            ->where('status', 'pending')
            ->firstOrFail();

        $this->authorize('view', $order);

        $order->update(['status' => 'cancelled']);

        return redirect()->route('cliente.dashboard')
            ->with('success', 'Orden cancelada correctamente.');
    }
}