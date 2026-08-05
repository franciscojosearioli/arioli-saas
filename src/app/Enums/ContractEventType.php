<?php

namespace App\Enums;

enum ContractEventType: string
{
    case Created = 'created';
    case Generated = 'generated';
    case Sent = 'sent';
    case Opened = 'opened';
    case ReminderSent = 'reminder_sent';
    case Signed = 'signed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Contrato creado',
            self::Generated => 'Documento generado',
            self::Sent => 'Enviado a firma',
            self::Opened => 'Abierto por el firmante',
            self::ReminderSent => 'Recordatorio enviado',
            self::Signed => 'Firmado',
            self::Rejected => 'Rechazado',
            self::Cancelled => 'Cancelado',
            self::Expired => 'Vencido',
        };
    }
}
