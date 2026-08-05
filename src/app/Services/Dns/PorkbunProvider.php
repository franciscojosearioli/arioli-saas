<?php

namespace App\Services\Dns;

use App\Contracts\DnsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Driver real de Porkbun (https://api.porkbun.com/api/json/v3) — registro de
 * dominios internacionales (nunca .ar/.com.ar, eso sigue siendo TAD manual,
 * ver memory/project_erp_evolution.md Ronda 7-8).
 */
class PorkbunProvider implements DnsProviderInterface
{
    private function client()
    {
        return Http::baseUrl(config('porkbun.base_url'))
            ->withHeaders([
                'X-API-Key'        => config('porkbun.api_key'),
                'X-Secret-API-Key' => config('porkbun.secret_key'),
                'Content-Type'     => 'application/json',
            ])
            ->timeout(15);
    }

    public function testConnection(): array
    {
        try {
            $response = $this->client()->post('/ping');

            if ($response->successful() && $response->json('status') === 'SUCCESS') {
                return ['success' => true, 'message' => 'Conectado a Porkbun — IP reportada: ' . $response->json('yourIp')];
            }

            return ['success' => false, 'message' => 'Porkbun respondió sin éxito: ' . $response->body()];
        } catch (\Throwable $e) {
            Log::error('Porkbun testConnection error', ['message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'No se pudo conectar a Porkbun: ' . $e->getMessage()];
        }
    }

    public function checkAvailability(string $domain): array
    {
        try {
            $response = $this->client()->post("/domain/checkDomain/{$domain}");

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['available' => false, 'message' => 'Porkbun no pudo verificar el dominio: ' . $response->body()];
            }

            $data      = $response->json('response') ?? [];
            $available = ($data['avail'] ?? 'no') === 'yes';
            $price     = $data['price'] ?? null;

            $message = $available
                ? 'Disponible' . ($price ? " — registro: USD {$price}" : '')
                : 'No disponible';

            return ['available' => $available, 'message' => $message, 'price_usd' => $price ? (float) $price : null];
        } catch (\Throwable $e) {
            Log::error('Porkbun checkAvailability error', ['domain' => $domain, 'message' => $e->getMessage()]);

            return ['available' => false, 'message' => 'Error al consultar disponibilidad: ' . $e->getMessage()];
        }
    }

    /**
     * Registra el dominio de verdad — cobra contra el saldo de la cuenta de
     * Porkbun apenas se llama, no hay confirmación intermedia de este lado.
     * Usa el perfil WHOIS default configurado en la cuenta de Porkbun (fuera
     * de Arioli) — Porkbun no pide datos de contacto por request, así que
     * 'owner_name'/'owner_email' de $data se ignoran a propósito acá.
     *
     * @param array $data ['domain' => string, ...]
     */
    public function register(array $data): array
    {
        $domain = $data['domain'] ?? null;

        if (! $domain) {
            return ['status' => 'error', 'message' => 'Falta el dominio a registrar.'];
        }

        try {
            $availability = $this->checkAvailability($domain);

            if (! $availability['available']) {
                return ['status' => 'error', 'message' => 'El dominio no está disponible: ' . $availability['message']];
            }

            $check = $this->client()->post("/domain/checkDomain/{$domain}");
            $price = $check->json('response.price');

            if (! $price) {
                return ['status' => 'error', 'message' => 'No se pudo obtener el costo de registro desde Porkbun.'];
            }

            $costCents = (int) round(((float) $price) * 100);

            $response = $this->client()->post("/domain/create/{$domain}", [
                'cost'         => $costCents,
                'agreeToTerms' => 'yes',
            ]);

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                Log::error('Porkbun register error', ['domain' => $domain, 'response' => $response->body()]);

                return ['status' => 'error', 'message' => 'Porkbun rechazó el registro: ' . $response->body()];
            }

            return ['status' => 'registered', 'message' => "Dominio {$domain} registrado correctamente en Porkbun."];
        } catch (\Throwable $e) {
            Log::error('Porkbun register exception', ['domain' => $domain, 'message' => $e->getMessage()]);

            return ['status' => 'error', 'message' => 'Error al registrar el dominio: ' . $e->getMessage()];
        }
    }

    public function listDnsRecords(string $domain): array
    {
        try {
            $response = $this->client()->post("/dns/retrieve/{$domain}");

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun no pudo listar los registros: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'OK', 'records' => $response->json('records') ?? []];
        } catch (\Throwable $e) {
            Log::error('Porkbun listDnsRecords error', ['domain' => $domain, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al listar registros DNS: ' . $e->getMessage()];
        }
    }

    public function createDnsRecord(string $domain, array $record): array
    {
        try {
            $payload = array_filter([
                'type'    => $record['type'] ?? null,
                'name'    => $record['name'] ?? '',
                'content' => $record['content'] ?? null,
                'ttl'     => $record['ttl'] ?? 600,
                'prio'    => $record['prio'] ?? null,
            ], fn ($v) => $v !== null);

            $response = $this->client()->post("/dns/create/{$domain}", $payload);

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun rechazó el registro DNS: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'Registro DNS creado correctamente.'];
        } catch (\Throwable $e) {
            Log::error('Porkbun createDnsRecord error', ['domain' => $domain, 'record' => $record, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al crear el registro DNS: ' . $e->getMessage()];
        }
    }

    public function updateDnsRecord(string $domain, string $recordId, array $record): array
    {
        try {
            $payload = array_filter([
                'type'    => $record['type'] ?? null,
                'name'    => $record['name'] ?? '',
                'content' => $record['content'] ?? null,
                'ttl'     => $record['ttl'] ?? 600,
                'prio'    => $record['prio'] ?? null,
            ], fn ($v) => $v !== null);

            $response = $this->client()->post("/dns/edit/{$domain}/{$recordId}", $payload);

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun rechazó la edición del registro: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'Registro DNS actualizado correctamente.'];
        } catch (\Throwable $e) {
            Log::error('Porkbun updateDnsRecord error', ['domain' => $domain, 'record_id' => $recordId, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al actualizar el registro DNS: ' . $e->getMessage()];
        }
    }

    public function deleteDnsRecord(string $domain, string $recordId): array
    {
        try {
            $response = $this->client()->post("/dns/delete/{$domain}/{$recordId}");

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun rechazó la baja del registro: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'Registro DNS eliminado correctamente.'];
        } catch (\Throwable $e) {
            Log::error('Porkbun deleteDnsRecord error', ['domain' => $domain, 'record_id' => $recordId, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al eliminar el registro DNS: ' . $e->getMessage()];
        }
    }

    public function getNameservers(string $domain): array
    {
        try {
            $response = $this->client()->post("/domain/getNs/{$domain}");

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun no pudo obtener los nameservers: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'OK', 'nameservers' => $response->json('ns') ?? []];
        } catch (\Throwable $e) {
            Log::error('Porkbun getNameservers error', ['domain' => $domain, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al obtener los nameservers: ' . $e->getMessage()];
        }
    }

    public function updateNameservers(string $domain, array $nameservers): array
    {
        try {
            $response = $this->client()->post("/domain/updateNs/{$domain}", [
                'ns' => array_values(array_filter($nameservers)),
            ]);

            if (! $response->successful() || $response->json('status') !== 'SUCCESS') {
                return ['success' => false, 'message' => 'Porkbun rechazó el cambio de nameservers: ' . $response->body()];
            }

            return ['success' => true, 'message' => 'Nameservers actualizados correctamente.'];
        } catch (\Throwable $e) {
            Log::error('Porkbun updateNameservers error', ['domain' => $domain, 'nameservers' => $nameservers, 'message' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Error al actualizar los nameservers: ' . $e->getMessage()];
        }
    }
}
