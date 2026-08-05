<?php

namespace App\Traits;

/**
 * Separa el costo real del proveedor (provider_cost) del honorario de gestión de
 * Arioli.dev (management_fee) — para no mezclar el costo real con la rentabilidad,
 * cada activo (ClientDomain, Hosting, SslCertificate, CloudflareService) expone su
 * precio final al cliente como la suma de ambos.
 */
trait HasCostSplit
{
    public function finalPrice(): float
    {
        return (float) ($this->provider_cost ?? 0) + (float) ($this->management_fee ?? 0);
    }
}
