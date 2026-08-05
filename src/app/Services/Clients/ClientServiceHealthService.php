<?php

namespace App\Services\Clients;

use App\Models\Client;

/**
 * Combina las dos señales de estado que ya existen por activo (el enum
 * operativo propio — ej. HostingStatus::Suspendido — y el vencimiento
 * derivado de HasRenewalStatus) en un único "Operativo"/"Atención" por
 * activo, más un resumen general — sin monitoreo real, cálculo declarativo
 * sobre datos ya trackeados.
 */
class ClientServiceHealthService
{
    private const PRIORITY = ['red' => 3, 'amber' => 2, 'gray' => 1, 'green' => 0];

    public function calculate(Client $client): array
    {
        // El SSL universal de Cloudflare viene incluido con el dominio y se
        // renueva solo — no es un activo separado que pueda quedar "en
        // atención" por vencimiento (ver ClientUpcomingRenewalsService).
        $trackableSsl = $client->sslCertificates->reject(
            fn ($ssl) => strcasecmp($ssl->provider, 'Cloudflare') === 0
        );

        $assets = collect()
            ->merge($client->hostings)
            ->merge($client->domains)
            ->merge($trackableSsl)
            ->merge($client->cloudflareServices)
            ->map(function ($asset) {
                $worst = $this->worst($asset->status->color(), $asset->renewalStatusColor());

                return [
                    'label'  => $asset->label(),
                    'color'  => $worst,
                    'health' => in_array($worst, ['red', 'amber'], true) ? 'Atención' : 'Operativo',
                ];
            })
            ->values();

        return [
            'assets'  => $assets,
            'overall' => $assets->contains(fn ($a) => $a['health'] === 'Atención') ? 'atencion' : 'operativo',
        ];
    }

    private function worst(string $a, string $b): string
    {
        return (self::PRIORITY[$a] ?? 0) >= (self::PRIORITY[$b] ?? 0) ? $a : $b;
    }
}
