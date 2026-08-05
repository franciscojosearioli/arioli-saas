<?php

namespace App\Services\Invoicing;

use App\Contracts\InvoicingProviderInterface;
use App\Models\Setting;
use InvalidArgumentException;

class InvoicingProviderManager
{
    public static function driver(?string $name = null): InvoicingProviderInterface
    {
        $name ??= Setting::get('afip.driver', 'afip');

        return match ($name) {
            'afip' => app(AfipProvider::class),
            default => throw new InvalidArgumentException("Driver de facturación desconocido: [{$name}]."),
        };
    }
}
