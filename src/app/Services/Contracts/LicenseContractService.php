<?php

namespace App\Services\Contracts;

use App\Enums\ClientEventType;
use App\Enums\ContractEventType;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\SignerRole;
use App\Enums\SignerStatus;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\Contract;
use App\Models\ContractEvent;
use App\Models\ContractTemplate;
use App\Models\License;

/**
 * Genera automáticamente, sin pedir firma, el contrato de una licencia
 * recién creada — reutiliza el flujo de Legales (plantillas, placeholders,
 * PDF, notificación) sin modificarlo: el "firmante" se crea ya en estado
 * Signed, así ContractNotificationService::notifySigned() funciona igual
 * que para un contrato firmado por el cliente.
 */
class LicenseContractService
{
    public function __construct(
        private readonly ContractTemplateRenderer $renderer,
        private readonly ContractNotificationService $notifier,
    ) {}

    public function generateForLicense(License $license, Client $client): Contract
    {
        $license->loadMissing('plan.product');

        $contact = $client->contacts()->where('is_primary', true)->first()
            ?? $client->contacts()->first();

        $template = ContractTemplate::where('type', ContractType::Licencia)
            ->where('active', true)
            ->first();

        $context = [
            'tenant_id'      => $license->tenant_id,
            'customer_name'  => $contact?->name ?? $client->name,
            'customer_email' => $contact?->email,
            'customer_cuit'  => null,
            'contractable'   => $license,
        ];

        $renderedContent = $this->renderer->render(
            $template->content ?? $this->defaultContent(),
            $context,
        );

        $contract = Contract::create([
            'tenant_id'             => $license->tenant_id,
            'client_id'             => $client->id,
            'contractable_type'     => License::class,
            'contractable_id'       => $license->id,
            'contract_template_id'  => $template?->id,
            'title'                 => "Contrato de licencia — {$license->plan?->product?->name}",
            'type'                  => ContractType::Licencia,
            'content'               => $renderedContent,
            'status'                => ContractStatus::Signed,
            'requires_signature'    => false,
        ]);

        ContractEvent::log($contract, ContractEventType::Created);
        ContractEvent::log($contract, ContractEventType::Generated);

        $signer = $contract->signers()->create([
            'role'      => SignerRole::Cliente,
            'name'      => $contact?->name ?? $client->name,
            'email'     => $contact?->email ?? '',
            'status'    => SignerStatus::Signed,
            'signed_at' => now(),
        ]);

        ContractEvent::log($contract, ContractEventType::Signed, $signer, [
            'auto'   => true,
            'reason' => 'Licencia — no requiere firma manual',
        ]);

        if ($signer->email) {
            $this->notifier->notifySigned($contract);
        }

        ClientEvent::log(
            $client,
            "Se generó el contrato de licencia \"{$license->plan?->product?->name}\"",
            ClientEventType::Contract,
            $contract,
        );

        return $contract;
    }

    private function defaultContent(): string
    {
        return <<<TEXT
        CONTRATO DE LICENCIA DE USO DE SOFTWARE

        Entre {{empresa_nombre}} (en adelante "el Proveedor") y {{cliente_nombre}} (en adelante
        "el Cliente"), se acuerda lo siguiente:

        1. OBJETO: El Proveedor otorga al Cliente una licencia de uso no exclusiva del sistema
        "{{sistema_nombre}}", identificado internamente como "{{tenant_id}}", bajo el plan
        "{{plan_nombre}}".

        2. VIGENCIA: La licencia tiene vigencia hasta el {{vencimiento}}, renovable según las
        condiciones comerciales vigentes al momento de la renovación.

        3. ACEPTACIÓN: Este contrato se genera automáticamente al momento de la contratación y no
        requiere firma manual — la contratación del servicio implica la aceptación de estos
        términos.

        Fecha: {{fecha_hoy}}
        TEXT;
    }
}
