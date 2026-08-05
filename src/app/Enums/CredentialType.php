<?php

namespace App\Enums;

enum CredentialType: string
{
    case WordPress = 'wordpress';
    case Cpanel = 'cpanel';
    case Cloudflare = 'cloudflare';
    case MercadoPago = 'mercadopago';
    case Google = 'google';
    case RedesSociales = 'redes_sociales';
    case Hosting = 'hosting';
    case Dominio = 'dominio';
    case Ftp = 'ftp';
    case Smtp = 'smtp';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::WordPress => 'WordPress',
            self::Cpanel => 'cPanel',
            self::Cloudflare => 'Cloudflare',
            self::MercadoPago => 'Mercado Pago',
            self::Google => 'Google',
            self::RedesSociales => 'Redes sociales',
            self::Hosting => 'Hosting',
            self::Dominio => 'Dominio',
            self::Ftp => 'FTP',
            self::Smtp => 'SMTP',
            self::Otro => 'Otro',
        };
    }
}
