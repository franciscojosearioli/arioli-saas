<?php

namespace App\Enums;

enum ClientServiceStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Cancelled => 'Cancelado',
            self::Completed => 'Completado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Cancelled => 'red',
            self::Completed => 'gray',
        };
    }
}
