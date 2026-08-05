<?php

namespace App\Enums;

enum ContactRole: string
{
    case Dueno = 'dueño';
    case Administrativo = 'administrativo';
    case Tecnico = 'tecnico';
    case Contador = 'contador';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Dueno => 'Dueño',
            self::Administrativo => 'Administrativo',
            self::Tecnico => 'Técnico',
            self::Contador => 'Contador',
            self::Otro => 'Otro',
        };
    }
}
