<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use InvalidArgumentException;

/**
 * Resuelve el driver de pagos activo. Agregar un proveedor nuevo (Stripe,
 * PayPal, etc.) es sumar una clase que implemente PaymentGatewayInterface
 * y un case acá — no toca el resto del sistema.
 */
class PaymentGatewayManager
{
    public static function driver(?string $name = null): PaymentGatewayInterface
    {
        $name ??= Setting::get('mercadopago.driver', 'mercadopago');

        return match ($name) {
            'mercadopago' => new MercadoPagoGateway(),
            default => throw new InvalidArgumentException("Driver de pagos desconocido: [{$name}]."),
        };
    }
}
