<?php

namespace App\Http\Controllers;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Models\Charge;
use App\Models\ClientEvent;
use App\Services\Fulfillment\ChargeFulfillmentService;
use App\Services\Payments\MercadoPagoWebhookVerifier;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook de Mercado Pago dedicado a los Charges del CRM — separado del
 * webhook de CheckoutController (que es para las Órdenes del checkout
 * público de licencias, no se toca). Mismo principio de seguridad: nunca
 * confiar en el body del webhook, siempre re-consultar el pago real a la
 * API antes de marcar algo como pagado.
 */
class ChargeWebhookController extends Controller
{
    public function handle(Request $request, MercadoPagoWebhookVerifier $verifier): JsonResponse
    {
        if (! $verifier->isValid($request)) {
            Log::warning('Charge webhook: firma inválida, request rechazado');

            return response()->json(['ok' => false], 401);
        }

        if ($request->get('type') !== 'payment') {
            return response()->json(['ok' => true]);
        }

        try {
            $payment = PaymentGatewayManager::driver()->getPayment($request->input('data.id'));

            $this->handlePayment($payment);
        } catch (\Throwable $e) {
            Log::error('Charge webhook error', ['message' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Mercado Pago solo permite registrar UNA url de webhook por aplicación —
     * por eso `CheckoutController::webhook()` (el endpoint que ya estaba
     * registrado para las Órdenes) también llama a este método antes de
     * seguir con su propia lógica, en vez de necesitar una segunda url.
     * Reutiliza el pago ya obtenido — no vuelve a llamar a la API de MP.
     *
     * @param array{id: mixed, status: string, external_reference: ?string} $payment
     * @return bool true si el pago era de un Charge del CRM (ya manejado), false si no.
     */
    public function handlePayment(array $payment): bool
    {
        $externalReference = $payment['external_reference'] ?? '';

        if (! str_starts_with($externalReference, 'charge_')) {
            return false;
        }

        $chargeId = (int) str_replace('charge_', '', $externalReference);
        $charge = Charge::find($chargeId);

        if ($charge && $payment['status'] === 'approved' && $charge->status !== ChargeStatus::Paid) {
            $charge->update(['mp_payment_id' => $payment['id']]);
            $charge->markAsPaid(\App\Enums\ChargePaymentMethod::MercadoPago->value);

            ClientEvent::log($charge->client, "Se cobró \"{$charge->concept}\"", ClientEventType::Charge, $charge);

            Log::info('Charge marcado como pagado vía webhook', ['charge_id' => $charge->id]);

            app(ChargeFulfillmentService::class)->fulfill($charge);
        }

        return true;
    }
}
