<?php

namespace App\Services\Marketplace;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * §08: "perfil de inmobiliaria — alimentada por Configuración". A
 * diferencia de Publicación/PublicacionCanal (multi-canal, con estado y
 * reintentos propios por diseño — §09), acá el destino es siempre uno
 * solo: el marketplace. No amerita la maquinaria de ChannelAdapter/
 * outbox — un push directo al guardar, best-effort, es proporcional.
 */
class PerfilInmobiliariaSync
{
    public static function sincronizar(Configuracion $configuracion): void
    {
        $url = config('marketplace.api_url');
        if (! $url || ! $configuracion->nombre_comercial) {
            return;
        }

        try {
            $cliente = Http::baseUrl($url)
                ->withToken(config('marketplace.api_token'))
                ->timeout(10);

            if ($host = config('marketplace.api_host')) {
                $cliente = $cliente->withHeaders(['Host' => $host]);
            }

            $cliente->put('/api/tenant-profile', [
                'tenant_id' => tenant('id'),
                'nombre_comercial' => $configuracion->nombre_comercial,
                'descripcion' => $configuracion->descripcion,
                'logo_url' => $configuracion->logo_url,
            ])->throw();
        } catch (\Throwable $e) {
            // Best-effort: un perfil desactualizado no debe bloquear que
            // el tenant guarde su propia Configuracion.
            Log::warning('No se pudo sincronizar el perfil de inmobiliaria con el marketplace', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
