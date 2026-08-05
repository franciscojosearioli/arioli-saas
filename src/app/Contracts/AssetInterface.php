<?php

namespace App\Contracts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Implementada por los "activos administrados" de un Client (hoy: ClientDomain, Hosting).
 * Permite que recordatorios (Etapa 4) y credenciales (Etapa 3) traten a todos los
 * activos por igual, sin if/else por tipo — ver config/asset_types.php.
 */
interface AssetInterface
{
    public function client(): BelongsTo;

    public function expiresAt(): ?Carbon;

    public function isAutoRenew(): bool;

    public function renewalPayer(): string;

    public function label(): string;
}
