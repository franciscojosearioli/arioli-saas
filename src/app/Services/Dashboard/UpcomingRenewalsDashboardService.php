<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\License;
use App\Services\Clients\ClientUpcomingRenewalsService;
use Illuminate\Support\Collection;

/**
 * Vencimientos próximos de TODO el negocio para el dashboard — no solo
 * licencias: dominios, hosting, SSL y Cloudflare de todos los clientes
 * también. Reutiliza ClientUpcomingRenewalsService cliente por cliente para
 * no duplicar qué cuenta como "por vencer" (ej. el SSL universal de
 * Cloudflare no se trackea ahí, ver esa clase).
 */
class UpcomingRenewalsDashboardService
{
    public function calculate(int $limit = 6): Collection
    {
        $perClient = new ClientUpcomingRenewalsService();

        // collect() explícito: ->flatMap() sobre un Eloquent Collection de Client
        // sigue devolviendo un Eloquent Collection aunque contenga stdClass, y su
        // merge() asume Models y llama getKey() — con collect() usamos el merge()
        // genérico de Support\Collection.
        $assetItems = collect(
            Client::with(['domains', 'hostings', 'sslCertificates', 'cloudflareServices'])
                ->get()
                ->flatMap(fn (Client $client) => $perClient->calculate($client)->map(fn ($asset) => (object) [
                    'label'      => "{$client->name} — {$asset->label()}",
                    'expires_at' => $asset->expiresAt(),
                ]))
        );

        $licenseItems = License::notDemo()
            ->with(['client', 'plan.product'])
            ->where('active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->get()
            ->filter(fn (License $license) => $license->client)
            ->map(fn (License $license) => (object) [
                'label'      => "{$license->client->name} — ".($license->plan?->product?->name ?? 'Licencia'),
                'expires_at' => $license->expires_at,
            ]);

        return $assetItems->merge($licenseItems)
            ->sortBy('expires_at')
            ->take($limit)
            ->values();
    }
}
