<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Borrador = 'borrador';
    case Enviada = 'enviada';
    case Aceptada = 'aceptada';
    case Rechazada = 'rechazada';

    public function label(): string
    {
        return match ($this) {
            self::Borrador => 'Borrador',
            self::Enviada => 'Enviada',
            self::Aceptada => 'Aceptada',
            self::Rechazada => 'Rechazada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Borrador => 'gray',
            self::Enviada => 'amber',
            self::Aceptada => 'green',
            self::Rechazada => 'red',
        };
    }
}
