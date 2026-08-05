<?php

namespace App\Support;

use App\Models\Notification;

class NotificationHelper
{
    public static function newOrder(\App\Models\Order $order): void
    {
        $productName = $order->plan->product->name ?? '';
        $periodLabel = $order->plan->period_label ?? '';
        $message = "{$order->customer_name} contrató {$productName} — {$periodLabel} por $" . number_format($order->amount, 0, ',', '.') . " ARS";

        Notification::create_notification(
            'new_order',
            'Nueva orden recibida',
            $message,
            'green',
            '/orders/' . $order->id,
            ['order_id' => $order->id, 'tenant_id' => $order->tenant_id]
        );
    }

    public static function newTenant(\App\Models\Tenant $tenant): void
    {
        Notification::create_notification(
            'new_tenant',
            'Nuevo cliente registrado',
            "{$tenant->name} se registró como nuevo cliente",
            'blue',
            '/tenants/' . $tenant->id,
            ['tenant_id' => $tenant->id]
        );
    }

    public static function licenceCancelled(\App\Models\License $license, string $tenantName): void
    {
        $productName = $license->plan->product->name ?? '';
        $periodLabel = $license->plan->period_label ?? '';
        $message = "{$tenantName} dio de baja su licencia de {$productName} ({$periodLabel})";

        Notification::create_notification(
            'license_cancelled',
            'Licencia dada de baja',
            $message,
            'red',
            '/licenses/' . $license->id,
            ['license_id' => $license->id, 'tenant_id' => $license->tenant_id]
        );
    }

    public static function licenseExpiring(\App\Models\License $license, int $days): void
    {
        $productName = $license->plan->product->name ?? '';
        $message = "El cliente {$license->tenant_id} tiene su licencia de {$productName} por vencer el {$license->expires_at->format('d/m/Y')}";

        Notification::create_notification(
            'license_expiring',
            "Licencia por vencer en {$days} días",
            $message,
            'yellow',
            '/licenses/' . $license->id,
            ['license_id' => $license->id, 'tenant_id' => $license->tenant_id, 'days' => $days]
        );
    }

    public static function licenseRenewed(\App\Models\License $license, string $tenantName): void
    {
        $productName = $license->plan->product->name ?? '';
        $message = "{$tenantName} renovó su licencia de {$productName} hasta {$license->expires_at->format('d/m/Y')}";

        Notification::create_notification(
            'license_renewed',
            'Licencia renovada',
            $message,
            'green',
            '/licenses/' . $license->id,
            ['license_id' => $license->id, 'tenant_id' => $license->tenant_id]
        );
    }

    /**
     * @param \App\Contracts\AssetInterface&\Illuminate\Database\Eloquent\Model $asset
     */
    public static function assetExpiring($asset, int $days): void
    {
        $client = $asset->client;
        $message = "{$client->name}: {$asset->label()} vence el {$asset->expiresAt()?->format('d/m/Y')}";

        Notification::create_notification(
            'asset_expiring',
            "{$asset->label()} por vencer en {$days} días",
            $message,
            'yellow',
            '/clients/' . $client->id,
            ['client_id' => $client->id, 'asset_type' => get_class($asset), 'asset_id' => $asset->getKey(), 'days' => $days]
        );
    }

    public static function domainPendingManualRegistration(\App\Models\ClientDomain $domain): void
    {
        $client = $domain->client;
        $message = "{$client->name}: el dominio {$domain->domain_name} está pendiente de registro manual";

        Notification::create_notification(
            'domain_pending_manual',
            'Dominio pendiente de registro',
            $message,
            'amber',
            '/clients/' . $client->id,
            ['client_id' => $client->id, 'domain_id' => $domain->id]
        );
    }

    public static function maintenanceBackupFailed(\App\Models\ClientService $service, string $reason): void
    {
        $client = $service->client;
        $message = "{$client->name}: falló el backup del mantenimiento mensual — {$reason}";

        Notification::create_notification(
            'maintenance_backup_failed',
            'Backup de mantenimiento fallido',
            $message,
            'red',
            '/clients/' . $client->id,
            ['client_id' => $client->id, 'service_id' => $service->id]
        );
    }

    public static function domainTransferRequested(\App\Models\ClientDomain $domain): void
    {
        $client = $domain->client;
        $message = "{$client->name} solicitó desde el portal la transferencia del dominio {$domain->domain_name} a otro registrador";

        Notification::create_notification(
            'domain_transfer_requested',
            'Solicitud de transferencia de dominio',
            $message,
            'amber',
            '/clients/' . $client->id,
            ['client_id' => $client->id, 'domain_id' => $domain->id]
        );
    }

    public static function contractPendingSignature(\App\Models\Contract $contract, int $daysPending): void
    {
        $message = "\"{$contract->title}\" está pendiente de firma hace {$daysPending} días";

        Notification::create_notification(
            'contract_pending_signature',
            'Contrato con firma pendiente',
            $message,
            'amber',
            '/legales/contratos/' . $contract->id,
            ['contract_id' => $contract->id, 'days' => $daysPending]
        );
    }
}