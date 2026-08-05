<?php

namespace App\Services\Hosting;

use App\Contracts\HostingPanelInterface;
use App\ValueObjects\HostingOperationResult;
use App\ValueObjects\HostingProvisionResult;

/**
 * Driver "manual" — a diferencia de ManualSignatureProvider (que devuelve un
 * estado "pendiente" porque la firma manual de verdad no terminó), este
 * simula un éxito de provisioning a propósito: permite probar el flujo
 * completo (Charge → Hosting → HostingAccount → mail → timeline) de punta a
 * punta sin depender de que HestiaCpProvider esté activo. Pasar a producción
 * real es solo cambiar `hosting_panel.driver` a `hestiacp`.
 */
class ManualHostingPanelProvider implements HostingPanelInterface
{
    public function createAccount(array $data): HostingProvisionResult
    {
        return HostingProvisionResult::success(
            username: $data['username'] ?? 'manual',
            panelUrl: null,
            message: 'Cuenta simulada — driver manual, sin panel real todavía.',
        );
    }

    public function getUsage(string $accountId): HostingOperationResult
    {
        return HostingOperationResult::success('Sin datos de uso — driver manual.', []);
    }

    public function suspend(string $accountId): HostingOperationResult
    {
        return HostingOperationResult::success('Suspensión simulada — driver manual.');
    }

    public function changePassword(string $accountId, string $newPassword): HostingOperationResult
    {
        return HostingOperationResult::success('Cambio de contraseña simulado — driver manual.');
    }

    public function testConnection(): HostingOperationResult
    {
        return HostingOperationResult::success('Modo manual — no requiere configuración.');
    }
}
