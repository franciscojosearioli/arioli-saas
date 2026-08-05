<?php

namespace App\Services\ExchangeRate;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cotización USD/ARS ("dólar oficial", venta) vía dolarapi.com — gratis, sin
 * auth. Usada para traducir precios en USD (Porkbun) a un cobro en ARS por
 * Mercado Pago. Nunca devuelve 0 ni falla en silencio — si la API no
 * responde, usa el fallback de config/exchange_rate.php y lo deja logueado.
 */
class DolarRateService
{
    public function getOficialVenta(): float
    {
        return Cache::remember('dolar_oficial_venta', now()->addHour(), function () {
            try {
                $response = Http::timeout(10)->get('https://dolarapi.com/v1/dolares/oficial');

                if ($response->successful() && $response->json('venta')) {
                    return (float) $response->json('venta');
                }

                Log::warning('DolarRateService: respuesta inesperada de dolarapi.com, usando fallback', [
                    'body' => $response->body(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('DolarRateService: dolarapi.com no respondió, usando fallback', [
                    'message' => $e->getMessage(),
                ]);
            }

            return (float) config('exchange_rate.fallback_usd_ars');
        });
    }
}
