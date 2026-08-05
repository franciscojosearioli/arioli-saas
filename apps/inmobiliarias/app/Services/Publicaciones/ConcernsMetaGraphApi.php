<?php

namespace App\Services\Publicaciones;

use App\Models\CuentaConectada;
use RuntimeException;

/**
 * Boilerplate compartido entre FacebookAdapter e InstagramAdapter — ambos
 * hablan con la misma Graph API, solo cambia el endpoint y el shape del
 * contenido.
 */
trait ConcernsMetaGraphApi
{
    private function cuentaConectada(string $canal): CuentaConectada
    {
        $cuenta = CuentaConectada::where('canal', $canal)->first();

        if (! $cuenta) {
            throw new RuntimeException("No hay ninguna cuenta de {$canal} conectada — conectala desde Configuración.");
        }

        if ($cuenta->requiereReconexion()) {
            throw new RuntimeException("La cuenta de {$canal} requiere reconexión — el token dejó de ser válido.");
        }

        return $cuenta;
    }

    private function graphUrl(string $path): string
    {
        $version = config('services.facebook.graph_version', 'v19.0');

        return "https://graph.facebook.com/{$version}/{$path}";
    }
}
