<?php

namespace App\Enums;

enum SslCertificateStatus: string
{
    case Activo = 'activo';
    case Vencido = 'vencido';
    case Pendiente = 'pendiente';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Vencido => 'Vencido',
            self::Pendiente => 'Pendiente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Activo => 'green',
            self::Vencido => 'red',
            self::Pendiente => 'amber',
        };
    }
}
