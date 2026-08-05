<?php

namespace App\Enums;

enum SignerStatus: string
{
    case Pending = 'pending';
    case Signed = 'signed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Signed => 'Firmado',
            self::Rejected => 'Rechazado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Signed => 'green',
            self::Rejected => 'red',
        };
    }
}
