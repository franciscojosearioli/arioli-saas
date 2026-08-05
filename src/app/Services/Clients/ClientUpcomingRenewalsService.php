<?php

namespace App\Services\Clients;

use App\Models\Client;
use Illuminate\Support\Collection;

/**
 * Agrega los vencimientos de todos los tipos de activo (Dominio, Hosting,
 * SSL, Cloudflare) de un cliente en una sola lista ordenada — misma fuente
 * de verdad para el Workspace admin y el Dashboard del portal cliente.
 */
class ClientUpcomingRenewalsService
{
    public function calculate(Client $client): Collection
    {
        return collect()
            ->merge($client->domains)
            ->merge($client->hostings)
            ->merge($this->trackableSslCertificates($client))
            ->merge($client->cloudflareServices)
            ->filter(fn ($asset) => $asset->expiresAt() && $asset->renewalStatusLabel() !== 'Activo')
            ->sortBy(fn ($asset) => $asset->expiresAt())
            ->values();
    }

    /**
     * El SSL universal de Cloudflare no es un servicio con vencimiento propio
     * que alguien tenga que gestionar — viene incluido mientras el dominio
     * esté detrás de Cloudflare y se renueva solo. Solo se trackean acá los
     * certificados de otros proveedores (Let's Encrypt manual, pagos, etc.)
     * que sí requieren una acción real antes de vencer.
     */
    private function trackableSslCertificates(Client $client): Collection
    {
        return $client->sslCertificates->reject(
            fn ($ssl) => strcasecmp($ssl->provider, 'Cloudflare') === 0
        );
    }
}
