<?php

namespace App\Services\Hosting;

use App\Contracts\HostingPanelInterface;
use App\ValueObjects\HostingOperationResult;
use App\ValueObjects\HostingProvisionResult;

/**
 * Driver real contra el HestiaCP instalado en host.arioli.dev. Orquesta y
 * traduce a los Value Objects de la interfaz — la ejecución real (SSH +
 * comandos v-*) vive en HestiaCliClient, inyectada acá para poder testear
 * cada capa por separado.
 */
class HestiaCpProvider implements HostingPanelInterface
{
    public function __construct(private readonly HestiaCliClient $client)
    {
    }

    public function createAccount(array $data): HostingProvisionResult
    {
        $result = $this->client->createHostingAccount(
            username: $data['username'],
            password: $data['password'],
            email: $data['email'],
            domain: $data['domain'],
            clientName: $data['client_name'] ?? 'Cliente Arioli',
            package: $data['package'] ?? null,
        );

        if (! $result['success']) {
            return HostingProvisionResult::failure($result['output']);
        }

        return HostingProvisionResult::success(
            username: $data['username'],
            panelUrl: config('hosting_panel.hestiacp.panel_url'),
            message: $result['output'],
        );
    }

    public function getUsage(string $accountId): HostingOperationResult
    {
        $result = $this->client->listUser($accountId);

        if (! $result['success']) {
            return HostingOperationResult::failure($result['output']);
        }

        $data = json_decode($result['output'], true) ?? [];

        return HostingOperationResult::success(data: $data);
    }

    public function suspend(string $accountId): HostingOperationResult
    {
        $result = $this->client->suspend($accountId);

        return $result['success']
            ? HostingOperationResult::success($result['output'])
            : HostingOperationResult::failure($result['output']);
    }

    public function changePassword(string $accountId, string $newPassword): HostingOperationResult
    {
        $result = $this->client->changePassword($accountId, $newPassword);

        return $result['success']
            ? HostingOperationResult::success($result['output'])
            : HostingOperationResult::failure($result['output']);
    }

    public function testConnection(): HostingOperationResult
    {
        $result = $this->client->listUser(config('hosting_panel.hestiacp.domain'));

        return $result['success']
            ? HostingOperationResult::success('Conexión con HestiaCP OK.')
            : HostingOperationResult::failure($result['output']);
    }
}
