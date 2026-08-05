<?php

namespace App\Enums;

enum ChargePaymentMethod: string
{
    case MercadoPago = 'mercado_pago';
    case Transferencia = 'transferencia';
    case Efectivo = 'efectivo';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::MercadoPago => 'Mercado Pago',
            self::Transferencia => 'Transferencia',
            self::Efectivo => 'Efectivo',
            self::Otro => 'Otro',
        };
    }
}
