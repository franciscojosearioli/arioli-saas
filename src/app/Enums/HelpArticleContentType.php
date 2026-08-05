<?php

namespace App\Enums;

enum HelpArticleContentType: string
{
    case Articulo = 'articulo';
    case Video = 'video';
    case Pdf = 'pdf';
    case Enlace = 'enlace';
    case Novedad = 'novedad';

    public function label(): string
    {
        return match ($this) {
            self::Articulo => 'Artículo',
            self::Video => 'Video',
            self::Pdf => 'PDF',
            self::Enlace => 'Enlace',
            self::Novedad => 'Novedad',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Articulo => '📄',
            self::Video => '🎬',
            self::Pdf => '📕',
            self::Enlace => '🔗',
            self::Novedad => '📣',
        };
    }
}
