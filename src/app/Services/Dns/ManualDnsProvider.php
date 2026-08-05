<?php

namespace App\Services\Dns;

use App\Contracts\DnsProviderInterface;

/**
 * El registro de dominios (.com.ar vía NIC.ar/TAD, o vía Porkbun para otros
 * TLD) sigue siendo un paso manual del admin — no hay API para el primer
 * caso, y todavía no se integró el segundo. Este driver solo deja rastro
 * de que hace falta hacerlo a mano.
 */
class ManualDnsProvider implements DnsProviderInterface
{
    public function checkAvailability(string $domain): array
    {
        return ['available' => false, 'message' => 'Verificar manualmente — sin integración automática todavía.'];
    }

    public function register(array $data): array
    {
        return ['status' => 'pending_manual', 'message' => 'Registrar manualmente en Porkbun/NIC.ar según el TLD.'];
    }

    public function testConnection(): array
    {
        return ['success' => true, 'message' => 'Modo manual — no requiere configuración.'];
    }

    public function listDnsRecords(string $domain): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene DNS administrado por Arioli — gestionalo directamente en tu registrador.'];
    }

    public function createDnsRecord(string $domain, array $record): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene DNS administrado por Arioli.'];
    }

    public function updateDnsRecord(string $domain, string $recordId, array $record): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene DNS administrado por Arioli.'];
    }

    public function deleteDnsRecord(string $domain, string $recordId): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene DNS administrado por Arioli.'];
    }

    public function getNameservers(string $domain): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene nameservers administrados por Arioli.'];
    }

    public function updateNameservers(string $domain, array $nameservers): array
    {
        return ['success' => false, 'message' => 'Este dominio no tiene nameservers administrados por Arioli.'];
    }
}
