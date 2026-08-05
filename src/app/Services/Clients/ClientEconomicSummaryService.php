<?php

namespace App\Services\Clients;

use App\Enums\BillingCycle;
use App\Models\Client;

/**
 * Mismo espíritu que Quote::economicSummary() — cuánto representa un
 * cliente para el negocio, sumando ClientService activos + el equivalente
 * mensual de sus Licencias activas.
 */
class ClientEconomicSummaryService
{
    public function calculate(Client $client): array
    {
        $activeServices = $client->services->filter(fn ($s) => $s->status->value === 'active');

        $mensual = $activeServices
            ->filter(fn ($s) => $s->billing_cycle === BillingCycle::Mensual)
            ->sum('amount');

        $anualDirecto = $activeServices
            ->filter(fn ($s) => $s->billing_cycle === BillingCycle::Anual)
            ->sum('amount');

        $licenciasMensual = $client->licenses
            ->filter(fn ($l) => $l->active && $l->plan && $l->plan->period_months > 0)
            ->sum(fn ($l) => $l->plan->final_price / $l->plan->period_months);

        $mensualTotal = (float) $mensual + (float) $licenciasMensual;

        return [
            'mensual' => $mensualTotal,
            'anual'   => ($mensualTotal * 12) + (float) $anualDirecto,
        ];
    }
}
