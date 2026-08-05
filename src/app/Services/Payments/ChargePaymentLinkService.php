<?php

namespace App\Services\Payments;

use App\Models\Charge;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Único punto que genera el link de pago de un Charge — usado desde
 * ChargeController::store()/regeneratePaymentLink() y desde
 * GenerateMonthlyServiceCharges, para no duplicar el bloque de
 * PaymentGatewayManager::driver()->createPreference() en los tres lugares.
 * No lanza excepciones: si Mercado Pago falla, el Charge queda sin link y
 * se puede reintentar — nunca bloquea la creación del cobro.
 *
 * El link de Mercado Pago cobra el saldo pendiente (`balance()`, no el
 * `amount` original — si ya se registró un pago parcial, ej. por
 * transferencia, MP solo debe cobrar lo que falta) + la comisión configurada
 * (mercadopago.fee_percent) para que a Arioli le quede ese saldo neto — el
 * monto exacto que MP va a cobrar queda guardado en `amount_with_fee`, así
 * el mail/portal muestran ese número sin tener que recalcularlo aparte. La
 * alternativa sin comisión (transferencia al alias configurado) también es
 * siempre por el saldo pendiente, nunca por el monto original ya parcialmente
 * cobrado.
 */
class ChargePaymentLinkService
{
    public function generate(Charge $charge): bool
    {
        try {
            $client = $charge->client;
            $feePercent = (float) Setting::get('mercadopago.fee_percent', 0);
            $amountWithFee = round($charge->balance() * (1 + $feePercent / 100), 2);

            $preference = PaymentGatewayManager::driver()->createPreference([
                'title'              => $charge->concept,
                'amount'             => $amountWithFee,
                'currency'           => $charge->currency->value,
                'payer_name'         => $client->name,
                'external_reference' => "charge_{$charge->id}",
            ]);

            $charge->update([
                'mp_preference_id' => $preference['id'],
                'payment_url'      => $preference['checkout_url'],
                'amount_with_fee'  => $amountWithFee,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('No se pudo generar el link de pago del cobro', [
                'charge_id' => $charge->id,
                'message'   => $e->getMessage(),
            ]);

            return false;
        }
    }
}
