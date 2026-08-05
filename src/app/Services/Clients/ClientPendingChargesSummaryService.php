<?php

namespace App\Services\Clients;

use App\Enums\ChargeStatus;
use App\Models\Client;

/**
 * Mismo espíritu que ClientEconomicSummaryService — opera sobre las
 * colecciones ya cargadas (`$client->charges` con `payments` eager-loaded),
 * sin queries nuevas. Los Charges ya agrupados en una orden de pago
 * (bundled_into_charge_id no nulo) no se cuentan aparte: ya están
 * representados por el Charge combinado.
 *
 * "pagado" y "pendiente" están basados en saldo real (Charge::amountPaid()/
 * balance()), no en el status binario — un cobro Pending con pagos parciales
 * ya registrados debe reflejar lo cobrado hasta ahora, no aparecer 100%
 * pendiente.
 */
class ClientPendingChargesSummaryService
{
    public function calculate(Client $client): array
    {
        $charges = $client->charges
            ->whereNull('bundled_into_charge_id')
            ->reject(fn ($c) => in_array($c->status, [ChargeStatus::Cancelled, ChargeStatus::Rejected], true));

        $pagado = $charges
            ->groupBy(fn ($c) => $c->currency->value)
            ->map(fn ($group) => $group->sum(fn ($c) => $c->amountPaid()));

        $pendiente = $charges
            ->groupBy(fn ($c) => $c->currency->value)
            ->map(fn ($group) => $group->sum(fn ($c) => $c->balance()));

        return [
            'pagado'    => $pagado,
            'pendiente' => $pendiente,
        ];
    }
}
