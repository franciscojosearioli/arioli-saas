<?php

namespace App\Services\Marketplace;

use App\Models\Desarrollo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// §08: "vista especial de Desarrollo: mapa interactivo con todas las
// unidades/lotes coloreadas por estado" y "perfil de constructora... con
// los Desarrollos a su cargo" necesitan que el marketplace conozca al
// Desarrollo como entidad real, no como el nombre_desarrollo suelto que
// hoy lleva la ficha de cada Propiedad. Mismo criterio que
// PerfilConstructoraSync: un solo destino, push directo best-effort, sin
// la maquinaria de ChannelAdapter/outbox (esa se justifica para
// Propiedad porque tiene múltiples canales — §09).
class DesarrolloSync
{
    public static function sincronizar(Desarrollo $desarrollo): void
    {
        $url = config('marketplace.api_url');
        if (! $url) {
            return;
        }

        try {
            $cliente = Http::baseUrl($url)
                ->withToken(config('marketplace.api_token'))
                ->timeout(10);

            if ($host = config('marketplace.api_host')) {
                $cliente = $cliente->withHeaders(['Host' => $host]);
            }

            $cliente->put('/api/desarrollos', [
                'tenant_id' => tenant('id'),
                'desarrollo_id' => $desarrollo->id,
                'constructora_id' => $desarrollo->constructora_id,
                'nombre' => $desarrollo->nombre,
                'tipo' => $desarrollo->tipo,
                'descripcion' => $desarrollo->descripcion,
                'provincia' => $desarrollo->provincia,
                'ciudad' => $desarrollo->ciudad,
                'barrio' => $desarrollo->barrio,
                'plano_maestro' => $desarrollo->plano_maestro,
                'ubicacion_wkt' => $desarrollo->ubicacionComoWkt(),
            ])->throw();
        } catch (\Throwable $e) {
            Log::warning('No se pudo sincronizar el desarrollo con el marketplace', [
                'desarrollo_id' => $desarrollo->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
