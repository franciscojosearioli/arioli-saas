<?php

namespace App\Services\Marketplace;

use App\Models\Constructora;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// §08: "perfil de constructora — una página por Constructora, con los
// Desarrollos a su cargo". Mismo criterio que PerfilInmobiliariaSync: un
// solo destino, push directo best-effort, sin la maquinaria de
// ChannelAdapter/outbox que sí se justifica para Propiedad (§09).
class PerfilConstructoraSync
{
    public static function sincronizar(Constructora $constructora): void
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

            $cliente->put('/api/constructora-profile', [
                'tenant_id' => tenant('id'),
                'constructora_id' => $constructora->id,
                'nombre' => $constructora->nombre,
                'descripcion' => $constructora->descripcion,
                'logo_url' => $constructora->logo,
            ])->throw();
        } catch (\Throwable $e) {
            Log::warning('No se pudo sincronizar el perfil de constructora con el marketplace', [
                'constructora_id' => $constructora->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
