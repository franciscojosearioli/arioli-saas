<?php

namespace App\Services\Clients;

use App\Enums\ChargeStatus;
use App\Enums\ContractStatus;
use App\Enums\ServiceType;
use App\Models\Client;

/**
 * Score de salud del cliente — 4 categorías de 25 puntos cada una, con
 * deducciones por señales concretas (nunca un promedio ciego). Fórmula ya
 * definida y aprobada, ver docs/erp-workspace-and-refinements.md §12.1.
 */
class ClientHealthScoreService
{
    public function calculate(Client $client): array
    {
        $infraestructura = $this->infraestructura($client);
        $seguridad       = $this->seguridad($client);
        $mantenimiento   = $this->mantenimiento($client);
        $documentacion   = $this->documentacion($client);

        return [
            'infraestructura' => $infraestructura,
            'seguridad'       => $seguridad,
            'mantenimiento'   => $mantenimiento,
            'documentacion'   => $documentacion,
            'total'           => $infraestructura + $seguridad + $mantenimiento + $documentacion,
        ];
    }

    private function infraestructura(Client $client): int
    {
        $points = 25;

        if ($client->hostings->isNotEmpty() && $client->hostings->contains(fn ($h) => $h->status->value !== 'activo')) {
            $points -= 25;
        }

        if ($client->domains->contains(fn ($d) => $d->renewalStatusLabel() === 'Vencido')) {
            $points -= 20;
        } elseif ($client->domains->contains(fn ($d) => $d->renewalStatusLabel() === 'Próximo a vencer')) {
            $points -= 10;
        }

        // Backups: sin tracking todavía en el modelo de datos — no se puede
        // evaluar esta señal hasta que exista (ver docs, nota explícita).

        return max(0, $points);
    }

    private function seguridad(Client $client): int
    {
        $points = 25;

        if ($client->sslCertificates->contains(fn ($s) => $s->renewalStatusLabel() === 'Vencido')) {
            $points -= 20;
        } elseif ($client->sslCertificates->contains(fn ($s) => $s->renewalStatusLabel() === 'Próximo a vencer')) {
            $points -= 10;
        }

        if ($client->cloudflareServices->contains(fn ($c) => $c->status->value === 'suspendido')) {
            $points -= 10;
        }

        $staleCredential = $client->credentials
            ->merge($client->domains->flatMap->credentials)
            ->merge($client->hostings->flatMap->credentials)
            ->contains(fn ($c) => ! $c->last_verified_at || $c->last_verified_at->lt(now()->subDays(90)));

        if ($staleCredential) {
            $points -= 5;
        }

        return max(0, $points);
    }

    private function mantenimiento(Client $client): int
    {
        $points = 25;

        $hasMaintenance = $client->services->contains(
            fn ($s) => $s->service_type === ServiceType::Mantenimiento && $s->status->value === 'active'
        );

        if (! $hasMaintenance) {
            $points -= 10;
        }

        $hasOverdueCharge = $client->charges->contains(
            fn ($c) => $c->status === ChargeStatus::Pending && $c->due_date && $c->due_date->isPast()
        );

        if ($hasOverdueCharge) {
            $points -= 15;
        }

        // Tickets críticos: Ticket cuelga de Tenant, no directo de Client —
        // se evalúa cuando exista una vía simple de resolverlo, no ahora.

        return max(0, $points);
    }

    private function documentacion(Client $client): int
    {
        $points = 25;

        if ($client->documents->isEmpty()) {
            $points -= 10;
        }

        $totalCredentials = $client->credentials->count()
            + $client->domains->sum(fn ($d) => $d->credentials->count())
            + $client->hostings->sum(fn ($h) => $h->credentials->count());

        if ($totalCredentials === 0) {
            $points -= 10;
        }

        $hasSignedContract = $client->contracts->contains(fn ($c) => $c->status === ContractStatus::Signed);

        if (! $hasSignedContract) {
            $points -= 5;
        }

        return max(0, $points);
    }
}
