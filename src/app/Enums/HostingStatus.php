<?php

namespace App\Enums;

enum HostingStatus: string
{
    case Pendiente = 'pendiente';
    case Activo = 'activo';
    case Suspendido = 'suspendido';
    case Cancelado = 'cancelado';
    case Migrando = 'migrando';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::Activo => 'Activo',
            self::Suspendido => 'Suspendido',
            self::Cancelado => 'Cancelado',
            self::Migrando => 'Migrando',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendiente => 'amber',
            self::Activo => 'green',
            self::Suspendido => 'red',
            self::Cancelado => 'gray',
            self::Migrando => 'amber',
        };
    }
}
