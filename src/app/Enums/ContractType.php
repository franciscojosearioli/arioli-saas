<?php

namespace App\Enums;

enum ContractType: string
{
    case Licencia = 'licencia';
    case Servicio = 'servicio';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Licencia => 'Licencia',
            self::Servicio => 'Servicio',
            self::Otro => 'Otro',
        };
    }
}
