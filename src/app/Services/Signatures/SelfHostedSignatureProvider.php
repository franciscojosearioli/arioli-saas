<?php

namespace App\Services\Signatures;

use App\Contracts\SignatureProviderInterface;
use App\Enums\ContractEventType;
use App\Models\ContractEvent;
use App\Models\ContractSigner;
use App\Services\Contracts\ContractNotificationService;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Driver de firma electrónica propio (gratuito) — opción por defecto.
 *
 * Genera un token de acceso único por firmante, envía el link vía
 * ContractNotificationService (nunca llama Mail:: directamente acá) y
 * registra el evento de negocio correspondiente en contract_events.
 * La verificación de identidad final ocurre en SignatureController al
 * completarse la firma (evidencia: IP, user agent, hash del contenido).
 */
class SelfHostedSignatureProvider implements SignatureProviderInterface
{
    public function __construct(private readonly ContractNotificationService $notifier) {}

    public function sendForSignature(array $document): array
    {
        if (! isset($document['signer']) || ! $document['signer'] instanceof ContractSigner) {
            throw new InvalidArgumentException('sendForSignature() requiere ["signer" => ContractSigner].');
        }

        /** @var ContractSigner $signer */
        $signer = $document['signer'];

        $signer->update([
            'signing_token'            => Str::random(64),
            'signing_token_expires_at' => now()->addDays(7),
        ]);

        $signingUrl = route('signature.show', $signer->signing_token);
        $isReminder = (bool) ($document['is_reminder'] ?? false);

        if ($isReminder) {
            $this->notifier->sendReminder($signer, $signingUrl);
            ContractEvent::log($signer->contract, ContractEventType::ReminderSent, $signer);
        } else {
            $this->notifier->sendSignatureRequest($signer, $signingUrl);
            ContractEvent::log($signer->contract, ContractEventType::Sent, $signer);
        }

        return [
            'external_id' => (string) $signer->id,
            'status'      => $signer->status->value,
            'signing_url' => $signingUrl,
        ];
    }

    public function getStatus(string $externalId): array
    {
        $signer = ContractSigner::findOrFail((int) $externalId);

        return [
            'external_id' => $externalId,
            'status'      => $signer->status->value,
            'signed_at'   => $signer->signed_at?->toISOString(),
        ];
    }

    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => 'Firma autogestionada (gratuita) — operativa, sin credenciales externas requeridas.',
        ];
    }
}
