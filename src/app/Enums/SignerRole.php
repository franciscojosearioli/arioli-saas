<?php

namespace App\Enums;

enum SignerRole: string
{
    case Cliente = 'cliente';
    case RepresentanteLegal = 'representante_legal';
    case SegundoFirmante = 'segundo_firmante';
    case Testigo = 'testigo';
    case Administrador = 'administrador';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Cliente => 'Cliente',
            self::RepresentanteLegal => 'Representante legal',
            self::SegundoFirmante => 'Segundo firmante',
            self::Testigo => 'Testigo',
            self::Administrador => 'Administrador',
            self::Otro => 'Otro',
        };
    }
}
