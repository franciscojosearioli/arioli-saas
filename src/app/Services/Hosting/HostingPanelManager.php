<?php

namespace App\Services\Hosting;

use App\Contracts\HostingPanelInterface;
use App\Models\Setting;
use InvalidArgumentException;

/**
 * Resuelve el driver de panel de hosting activo. Agregar un panel nuevo
 * (Plesk, cPanel, DirectAdmin) es sumar una clase que implemente
 * HostingPanelInterface y un case acá — no toca el resto del sistema.
 * Default 'manual' hasta que el driver real (hestiacp) esté verificado
 * end-to-end contra el servidor de producción.
 */
class HostingPanelManager
{
    public static function driver(?string $name = null): HostingPanelInterface
    {
        $name ??= Setting::get('hosting_panel.driver', 'manual');

        return match ($name) {
            'manual'   => app(ManualHostingPanelProvider::class),
            'hestiacp' => app(HestiaCpProvider::class),
            default    => throw new InvalidArgumentException("Driver de panel de hosting desconocido: [{$name}]."),
        };
    }
}
