<?php

namespace App\Enums;

enum DomainStatus: string
{
    case Activo = 'activo';
    case Suspendido = 'suspendido';
    case Transferencia = 'transferencia';
    case Pendiente = 'pendiente';
    case Expirado = 'expirado';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Suspendido => 'Suspendido',
            self::Transferencia => 'En transferencia',
            self::Pendiente => 'Pendiente',
            self::Expirado => 'Expirado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Activo => 'green',
            self::Suspendido => 'red',
            self::Transferencia => 'amber',
            self::Pendiente => 'amber',
            self::Expirado => 'red',
        };
    }
}
