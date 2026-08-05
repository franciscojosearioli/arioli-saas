<?php

namespace App\Enums;

enum BillingCycle: string
{
    case Mensual = 'mensual';
    case Anual = 'anual';
    case Unico = 'unico';

    public function label(): string
    {
        return match ($this) {
            self::Mensual => 'Mensual',
            self::Anual => 'Anual',
            self::Unico => 'Único',
        };
    }
}
