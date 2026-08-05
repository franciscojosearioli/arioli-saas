<?php

namespace App\Enums;

enum ClientEventType: string
{
    case Created = 'created';
    case Project = 'project';
    case Service = 'service';
    case Job = 'job';
    case Contract = 'contract';
    case Quote = 'quote';
    case Charge = 'charge';
    case Domain = 'domain';
    case Hosting = 'hosting';
    case Note = 'note';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Cliente creado',
            self::Project => 'Proyecto',
            self::Service => 'Servicio',
            self::Job => 'Trabajo',
            self::Contract => 'Contrato',
            self::Quote => 'Propuesta',
            self::Charge => 'Cobro',
            self::Domain => 'Dominio',
            self::Hosting => 'Hosting',
            self::Note => 'Nota',
        };
    }
}
