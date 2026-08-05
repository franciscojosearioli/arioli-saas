<?php

namespace App\Enums;

enum CloudflareServiceStatus: string
{
    case Activo = 'activo';
    case Suspendido = 'suspendido';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Suspendido => 'Suspendido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Activo => 'green',
            self::Suspendido => 'red',
        };
    }
}
