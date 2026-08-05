<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Order;
use App\Models\Plan;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;

class CheckoutController extends Controller
{
    private function initMP(): void
    {
        MercadoPagoConfig::setAccessToken(
            config('mercadopago.mode') === 'production'
                ? config('mercadopago.access_token_prod')
                : config('mercadopago.access_token_test')
        );
    }

    // Mostrar formulario de checkout
    public function show(Plan $plan)
    {
        $this->assertSellable($plan);

        $plan->load('product');
        $domainSuffix = '.' . $plan->product->public_domain . '.' . config('app.tenant_domain');
        return view('landing.checkout', compact('plan', 'domainSuffix'));
    }

    /**
     * Los planes "Demo" (price=0, period_months=0) están reservados al tenant
     * técnico "demo" de cada producto — nunca deben poder comprarse desde acá,
     * ni siquiera visitando /checkout/{id} directamente con el id de un Demo.
     */
    private function assertSellable(Plan $plan): void
    {
        abort_unless(
            Plan::sellable()->whereKey($plan->id)->exists(),
            404,
        );
    }

    // Verificación en vivo del email mientras el usuario completa el formulario
    public function verifyEmail(Request $request, EmailVerificationService $verifier)
    {
        $validated = $request->validate(['email' => 'required|string|max:255']);

        return response()->json($verifier->verify($validated['email']));
    }

    // Crear preferencia de pago en MP
    public function process(Request $request, Plan $plan)
    {
        $this->assertSellable($plan);

    Log::info('Checkout process started', [
        'plan_id' => $plan->id,
        'request' => $request->all(),
    ]);
        $rules = [
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email:rfc,dns|max:255',
            'customer_company' => 'required|string|max:63|alpha_dash',
        ];

        $turnstileConfigured = config('services.turnstile.sitekey') && config('services.turnstile.secret');
        if ($turnstileConfigured) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $validated = $request->validate($rules);

        if ($turnstileConfigured) {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => config('services.turnstile.secret'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if (! ($response->json('success') ?? false)) {
                throw ValidationException::withMessages([
                    'cf-turnstile-response' => 'La verificación de seguridad falló, intentá nuevamente.',
                ]);
            }
        }

        // Verificar que el subdominio no esté en uso para este producto
        $tenantId    = \Illuminate\Support\Str::slug($validated['customer_company']);
        $domain      = $tenantId . '.' . $plan->product->public_domain . '.' . config('app.tenant_domain');

        // Subdominios reservados del sistema (no disponibles para clientes)
        $reserved = ['demo', 'admin', 'api', 'cliente', 'www', 'mail', 'ftp', 'app'];
        if (in_array($tenantId, $reserved)) {
            return back()->withErrors([
                'customer_company' => 'Ese nombre está reservado. Elegí otro.',
            ])->withInput();
        }

        if (Domain::where('domain', $domain)->exists()) {
            return back()->withErrors([
                'customer_company' => 'Ese nombre ya está en uso. Elegí otro.',
            ])->withInput();
        }

        // Crear order pendiente
        $order = Order::create([
            'plan_id'          => $plan->id,
            'customer_name'    => $validated['customer_name'],
            'customer_email'   => $validated['customer_email'],
            'customer_company' => $validated['customer_company'],
            'amount'           => $plan->price,
            'status'           => 'pending',
        ]);

        // Crear preferencia en MercadoPago
        try {
            $this->initMP();
            $client = new PreferenceClient();
            $preference = $client->create([
                'items' => [
                    [
                        'title'       => $plan->product->name . ' — ' . $plan->period_label,
                        'quantity'    => 1,
                        'unit_price'  => (float) $plan->price,
                        'currency_id' => 'ARS',
                    ],
                ],
                'payer' => [
                    'name'  => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                ],
                'external_reference' => $order->uuid,
                'statement_descriptor' => 'Arioli.dev',
            ]);

            $order->update(['mp_preference_id' => $preference->id]);

            $url = config('mercadopago.mode') === 'production'
                ? $preference->init_point
                : $preference->sandbox_init_point;

            return redirect($url);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MP API Error', [
                'message'  => $e->getMessage(),
                'response' => $e->getApiResponse()?->getContent(),
                'status'   => $e->getApiResponse()?->getStatusCode(),
            ]);
            dd($e->getMessage(), $e->getApiResponse()?->getContent());
        } catch (\Exception $e) {
            Log::error('MP General Error: ' . $e->getMessage());
            dd($e->getMessage());
        }
    }

    // Pago aprobado
    public function success(Request $request)
    {
        $paymentId         = $request->get('payment_id');
        $externalReference = $request->get('external_reference');
        $status            = $request->get('status');

        $order = Order::where('uuid', $externalReference)->first();

        if (!$order) {
            return redirect(route('landing.home'))->with('error', 'Orden no encontrada.');
        }

        if ($status === 'approved' && $order->isPending()) {
            $this->provisionTenant($order, $paymentId);
        }

        return view('landing.checkout-success', compact('order'));
    }

    // Pago rechazado
    public function failure(Request $request)
    {
        $externalReference = $request->get('external_reference');
        $order = Order::where('uuid', $externalReference)->first();

        if ($order) {
            $order->update(['status' => 'rejected']);
        }

        return view('landing.checkout-failure', compact('order'));
    }

    // Pago pendiente
    public function pending(Request $request)
    {
        $externalReference = $request->get('external_reference');
        $order = Order::where('uuid', $externalReference)->first();

        return view('landing.checkout-pending', compact('order'));
    }

    // Webhook de MercadoPago
    public function webhook(Request $request, \App\Services\Payments\MercadoPagoWebhookVerifier $verifier)
    {
        if (! $verifier->isValid($request)) {
            Log::warning('Checkout webhook: firma inválida, request rechazado');

            return response()->json(['ok' => false], 401);
        }

        $type = $request->get('type');

        if ($type !== 'payment') {
            return response()->json(['ok' => true]);
        }

        $paymentId = $request->input('data.id');

        try {
            $this->initMP();

            $paymentClient = new \MercadoPago\Client\Payment\PaymentClient();
            $payment       = $paymentClient->get($paymentId);

            // Mercado Pago solo permite una url de webhook por aplicación — esta, ya
            // registrada para las Órdenes, también recibe los pagos de los Charges del
            // CRM (Etapa 5). Si era un Charge, ya quedó resuelto acá, sin tocar nada más.
            $handledAsCharge = app(\App\Http\Controllers\ChargeWebhookController::class)->handlePayment([
                'id'                  => $payment->id,
                'status'              => $payment->status,
                'external_reference'  => $payment->external_reference,
            ]);

            if ($handledAsCharge) {
                return response()->json(['ok' => true]);
            }

            $order = Order::where('uuid', $payment->external_reference)->first();

            if (!$order) {
                return response()->json(['ok' => true]);
            }

            $order->update([
                'mp_payment_id' => $paymentId,
                'mp_status'     => $payment->status,
                'mp_data'       => (array) $payment,
            ]);

            if ($payment->status === 'approved' && $order->isPending()) {
                $this->provisionTenant($order, $paymentId);
            }

        } catch (\Exception $e) {
            Log::error('MP Webhook error: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    // Despacha el Job de provisioning del sistema externo según el producto contratado
    private function dispatchProvisioning(string $productSlug, string $publicDomain, string $tenantId, Order $order, ?string $password): void
    {
        $adminPassword = $password ?? \Illuminate\Support\Str::random(12);

        match ($productSlug) {
            'loteos' => \App\Jobs\ProvisionLoteosInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            'tallerpro' => \App\Jobs\ProvisionTallerProInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            'historias-clinicas' => \App\Jobs\ProvisionHistoriasInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            'turnos' => \App\Jobs\ProvisionTurnosInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            'subastas' => \App\Jobs\ProvisionSubastasInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            'chatia' => \App\Jobs\ProvisionChatiaInstance::dispatch(
                $tenantId,
                $productSlug,
                $publicDomain,
                $order->customer_name,
                $order->customer_email,
                $adminPassword,
            ),
            default => null,
        };
    }

    private function provisionTenant(Order $order, string $paymentId): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $tenantId = \Illuminate\Support\Str::slug($order->customer_company);
            $plan     = $order->plan;
            $password = null;

            // ¿El tenant ya existe? (cliente que compra otro sistema)
            $tenant = \App\Models\Tenant::find($tenantId);

            if (!$tenant) {
                // Nuevo cliente — crear tenant
                $tenant = \App\Models\Tenant::create([
                    'id'    => $tenantId,
                    'name'  => $order->customer_name,
                    'email' => $order->customer_email,
                ]);

                // Crear usuario del cliente en el portal Arioli
                $password = \Illuminate\Support\Str::random(12);
                $user = \App\Models\User::create([
                    'name'      => $order->customer_name,
                    'email'     => $order->customer_email,
                    'password'  => bcrypt($password),
                    'tenant_id' => $tenant->id,
                ]);
                $user->assignRole('tenant_admin');
            } else {
                // Cliente existente — no crear usuario nuevo
                $user = \App\Models\User::where('tenant_id', $tenantId)->first();
            }

            // Verificar si ya tiene licencia activa para este producto
            $existingLicense = \App\Models\License::where('tenant_id', $tenantId)
                ->where('active', true)
                ->whereHas('plan', fn($q) => $q->where('product_id', $plan->product_id))
                ->first();

            if ($existingLicense) {
                // Renovar licencia existente (el dominio ya existe, no se crea uno nuevo)
                $existingLicense->renew($plan->id, $plan->period_months);
                $license = $existingLicense;
            } else {
                // Nueva licencia — dominio usa public_domain: {tenantId}.{public_domain}.{tenantDomain}
                $license = \App\Models\License::createForTenant(
                    $tenantId,
                    $plan->id,
                    $plan->period_months,
                    $plan->product
                );

                // Disparar provisioning del sistema externo en segundo plano
                $this->dispatchProvisioning($plan->product->slug, $plan->product->public_domain, $tenantId, $order, $password);
            }

            // Actualizar orden
            $order->update([
                'tenant_id'     => $tenant->id,
                'status'        => 'approved',
                'mp_payment_id' => $paymentId,
                'paid_at'       => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();
            
            // Notificaciones
            \App\Support\NotificationHelper::newTenant($tenant);
            \App\Support\NotificationHelper::newOrder($order->fresh()->load('plan.product'));

            // Enviar email
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->send(new \App\Mail\WelcomeMail($order, $tenant, $password ?? ''));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('WelcomeMail error: ' . $e->getMessage());
            }

            \Illuminate\Support\Facades\Log::info('Tenant provisioned', [
                'tenant_id' => $tenantId,
                'plan'      => $plan->name,
                'license'   => $license->id,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Provision tenant error', [
                'message' => $e->getMessage(),
                'order'   => $order->uuid,
                'trace'   => $e->getTraceAsString(),
            ]);
            $order->update(['status' => 'provision_failed']);
            throw $e;
        }
    }
}