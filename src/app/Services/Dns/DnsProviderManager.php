<?php

namespace App\Services\Dns;

use App\Contracts\DnsProviderInterface;
use App\Models\Setting;
use InvalidArgumentException;

/**
 * Resuelve el driver de registro/DNS activo. Deja el lugar exacto para un
 * PorkbunProvider futuro — hoy solo existe el driver manual (el registro de
 * dominios sigue siendo un paso humano, ver decisiones documentadas en
 * memory/project_erp_evolution.md).
 */
class DnsProviderManager
{
    public static function driver(?string $name = null): DnsProviderInterface
    {
        $name ??= Setting::get('dns_provider.driver', 'manual');

        return match ($name) {
            'manual'  => app(ManualDnsProvider::class),
            'porkbun' => app(PorkbunProvider::class),
            default   => throw new InvalidArgumentException("Driver de DNS/registro desconocido: [{$name}]."),
        };
    }
}
