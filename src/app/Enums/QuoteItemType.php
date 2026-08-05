<?php

namespace App\Enums;

enum QuoteItemType: string
{
    case Service = 'service';
    case Job = 'job';

    public function label(): string
    {
        return match ($this) {
            self::Service => 'Servicio recurrente',
            self::Job => 'Trabajo puntual',
        };
    }
}
