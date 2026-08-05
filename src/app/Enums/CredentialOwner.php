<?php

namespace App\Enums;

enum CredentialOwner: string
{
    case Cliente = 'cliente';
    case Arioli = 'arioli';
    case ProveedorExterno = 'proveedor_externo';

    public function label(): string
    {
        return match ($this) {
            self::Cliente => 'Del cliente',
            self::Arioli => 'De Arioli',
            self::ProveedorExterno => 'Proveedor externo',
        };
    }
}
