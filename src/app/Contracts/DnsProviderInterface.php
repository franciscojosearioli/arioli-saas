<?php

namespace App\Contracts;

interface DnsProviderInterface
{
    /**
     * Consulta si un dominio está disponible para registrar.
     *
     * @return array{available: bool, message: string}
     */
    public function checkAvailability(string $domain): array;

    /**
     * Registra un dominio a nombre del titular indicado.
     *
     * @param array $data ['domain','owner_name','owner_email',...]
     * @return array{status: string, message: string}
     */
    public function register(array $data): array;

    /**
     * @return array{success: bool, message: string}
     */
    public function testConnection(): array;

    /**
     * @return array{success: bool, message: string, records?: array}
     */
    public function listDnsRecords(string $domain): array;

    /**
     * @param array $record ['type','name','content','ttl'?,'prio'?]
     * @return array{success: bool, message: string}
     */
    public function createDnsRecord(string $domain, array $record): array;

    /**
     * @param array $record ['type','name','content','ttl'?,'prio'?]
     * @return array{success: bool, message: string}
     */
    public function updateDnsRecord(string $domain, string $recordId, array $record): array;

    /**
     * @return array{success: bool, message: string}
     */
    public function deleteDnsRecord(string $domain, string $recordId): array;

    /**
     * @return array{success: bool, message: string, nameservers?: array}
     */
    public function getNameservers(string $domain): array;

    /**
     * @param array<int, string> $nameservers
     * @return array{success: bool, message: string}
     */
    public function updateNameservers(string $domain, array $nameservers): array;
}
