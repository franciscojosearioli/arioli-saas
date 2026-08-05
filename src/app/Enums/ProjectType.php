<?php

namespace App\Enums;

enum ProjectType: string
{
    case SitioWeb = 'sitio_web';
    case TiendaOnline = 'tienda_online';
    case Blog = 'blog';
    case SistemaAMedida = 'sistema_a_medida';
    case Landing = 'landing';
    case Intranet = 'intranet';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::SitioWeb => 'Sitio web',
            self::TiendaOnline => 'Tienda online',
            self::Blog => 'Blog',
            self::SistemaAMedida => 'Sistema a medida',
            self::Landing => 'Landing page',
            self::Intranet => 'Intranet',
            self::Otro => 'Otro',
        };
    }
}
