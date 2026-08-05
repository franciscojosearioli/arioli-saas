<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Draft = 'draft';
    case PendingSignature = 'pending_signature';
    case Signed = 'signed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::PendingSignature => 'Pendiente de firma',
            self::Signed => 'Firmado',
            self::Rejected => 'Rechazado',
            self::Cancelled => 'Cancelado',
            self::Expired => 'Vencido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::PendingSignature => 'amber',
            self::Signed => 'green',
            self::Rejected => 'red',
            self::Cancelled => 'red',
            self::Expired => 'gray',
        };
    }
}
