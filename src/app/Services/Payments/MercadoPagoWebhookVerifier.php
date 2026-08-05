<?php

namespace App\Services\Payments;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Verifica la firma (header `x-signature`) que Mercado Pago manda en cada
 * webhook, según su esquema documentado: HMAC-SHA256 de un "manifest"
 * `id:{data.id};request-id:{x-request-id};ts:{ts};` usando la clave secreta
 * configurada en el panel de la aplicación.
 *
 * Es una capa extra sobre la protección que ya existe (nunca se confía en el
 * body del webhook, siempre se re-consulta el pago real a la API antes de
 * marcar algo como pagado) — si no hay clave configurada todavía, deja pasar
 * sin romper nada (fail-open), para no bloquear el webhook mientras se
 * termina de configurar en el panel de Mercado Pago.
 */
class MercadoPagoWebhookVerifier
{
    public function isValid(Request $request): bool
    {
        $secret = Setting::get('mercadopago.webhook_secret', config('mercadopago.webhook_secret'));

        if (! $secret) {
            return true;
        }

        $signatureHeader = $request->header('x-signature');
        $requestId = $request->header('x-request-id');
        $dataId = $request->query('data.id') ?? $request->query('data_id') ?? $request->input('data.id');

        if (! $signatureHeader || ! $dataId) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $chunk) {
            [$key, $value] = array_pad(explode('=', trim($chunk), 2), 2, null);
            if ($key !== null) {
                $parts[$key] = $value;
            }
        }

        $ts = $parts['ts'] ?? null;
        $v1 = $parts['v1'] ?? null;

        if (! $ts || ! $v1) {
            return false;
        }

        $manifest = "id:" . strtolower((string) $dataId) . ";"
            . ($requestId ? "request-id:{$requestId};" : '')
            . "ts:{$ts};";

        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $v1);
    }
}
