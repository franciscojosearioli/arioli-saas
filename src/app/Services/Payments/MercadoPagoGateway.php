<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\PaymentMethod\PaymentMethodClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

/**
 * Envuelve el SDK oficial de Mercado Pago detrás de PaymentGatewayInterface.
 *
 * Nota: esta clase NO reemplaza el uso actual de MercadoPago en
 * CheckoutController (que sigue funcionando exactamente igual). Existe para
 * que módulos nuevos (Facturación, Cobros, etc.) programen contra la
 * interfaz en vez de acoplarse directamente al SDK.
 */
class MercadoPagoGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        $mode = Setting::get('mercadopago.mode', config('mercadopago.mode'));

        $token = $mode === 'production'
            ? Setting::get('mercadopago.access_token_prod', config('mercadopago.access_token_prod'))
            : Setting::get('mercadopago.access_token_test', config('mercadopago.access_token_test'));

        MercadoPagoConfig::setAccessToken((string) $token);
    }

    public function createPreference(array $data): array
    {
        $client = new PreferenceClient();

        $preference = $client->create([
            'items' => [[
                'title'       => $data['title'],
                'quantity'    => 1,
                'unit_price'  => (float) $data['amount'],
                'currency_id' => $data['currency'] ?? 'ARS',
            ]],
            'payer' => [
                'name'  => $data['payer_name'] ?? null,
                'email' => $data['payer_email'] ?? null,
            ],
            'external_reference' => $data['external_reference'] ?? null,
        ]);

        $mode = Setting::get('mercadopago.mode', config('mercadopago.mode'));

        return [
            'id'           => $preference->id,
            'checkout_url' => $mode === 'production' ? $preference->init_point : $preference->sandbox_init_point,
        ];
    }

    public function getPayment(string $paymentId): array
    {
        $payment = (new PaymentClient())->get($paymentId);

        return [
            'id'                  => $payment->id,
            'status'              => $payment->status,
            'amount'              => $payment->transaction_amount,
            'external_reference'  => $payment->external_reference,
        ];
    }

    public function testConnection(): array
    {
        try {
            // Llamada liviana y de solo lectura: listar métodos de pago habilitados
            // confirma que el access_token configurado es válido.
            (new PaymentMethodClient())->list();

            return ['success' => true, 'message' => 'Conexión con Mercado Pago exitosa.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'No se pudo conectar con Mercado Pago: '.$e->getMessage()];
        }
    }
}
