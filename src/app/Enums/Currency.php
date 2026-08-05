<?php

namespace App\Enums;

enum Currency: string
{
    case ARS = 'ARS';
    case USD = 'USD';
    case EUR = 'EUR';

    public function label(): string
    {
        return match ($this) {
            self::ARS => 'Pesos argentinos',
            self::USD => 'Dólares',
            self::EUR => 'Euros',
        };
    }
}
