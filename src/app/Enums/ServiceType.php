<?php

namespace App\Enums;

enum ServiceType: string
{
    case Mantenimiento = 'mantenimiento';
    case Seo = 'seo';
    case GoogleAds = 'google_ads';
    case Backup = 'backup';
    case DesarrolloAMedida = 'desarrollo_a_medida';
    case Consultoria = 'consultoria';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Mantenimiento => 'Mantenimiento',
            self::Seo => 'SEO',
            self::GoogleAds => 'Google Ads',
            self::Backup => 'Backup',
            self::DesarrolloAMedida => 'Desarrollo a medida',
            self::Consultoria => 'Consultoría',
            self::Otro => 'Otro',
        };
    }
}
