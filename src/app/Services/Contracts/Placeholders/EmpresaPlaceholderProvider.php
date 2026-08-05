<?php

namespace App\Services\Contracts\Placeholders;

use App\Contracts\PlaceholderProviderInterface;
use App\Models\Setting;

class EmpresaPlaceholderProvider implements PlaceholderProviderInterface
{
    public function supports(array $context): bool
    {
        return true;
    }

    public function resolve(array $context): array
    {
        return [
            'empresa_nombre' => Setting::get('empresa.nombre', 'Arioli'),
            'empresa_cuit'   => Setting::get('empresa.cuit', ''),
        ];
    }
}
