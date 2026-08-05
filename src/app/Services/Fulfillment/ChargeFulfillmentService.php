<?php

namespace App\Services\Fulfillment;

use App\Jobs\ProvisionHostingAccount;
use App\Jobs\RegisterPorkbunDomain;
use App\Models\Charge;
use App\Models\ClientDomain;
use App\Models\Hosting;

/**
 * Dispatcher explícito: qué Job correr cuando un Charge se paga, según el
 * tipo de `chargeable`. Mismo patrón que ya usa
 * CheckoutController::dispatchProvisioning() para licencias SaaS (un match
 * por tipo, sin abstracción de "Producto" genérica) — agregar un segundo
 * tipo de servicio el día de mañana es sumar un case acá, no rediseñar nada.
 */
class ChargeFulfillmentService
{
    public function fulfill(Charge $charge): void
    {
        match ($charge->chargeable_type) {
            Hosting::class => ProvisionHostingAccount::dispatch($charge->chargeable),
            ClientDomain::class => RegisterPorkbunDomain::dispatch($charge->chargeable),
            default => null,
        };

        // Si este Charge es una orden de pago que agrupa a otros, cada uno
        // dispara su propio fulfillment igual que si se hubiera cobrado suelto.
        foreach ($charge->bundledItems as $item) {
            $this->fulfill($item);
        }
    }
}
