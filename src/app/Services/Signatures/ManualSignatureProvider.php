<?php

namespace App\Services\Signatures;

use App\Contracts\SignatureProviderInterface;

/**
 * Driver "manual": el admin marca el documento como firmado fuera del
 * sistema (papel, email, etc.), sin evidencia electrónica. Útil como
 * fallback mientras no haya firma autogestionada ni proveedor externo.
 */
class ManualSignatureProvider implements SignatureProviderInterface
{
    public function sendForSignature(array $document): array
    {
        return [
            'external_id' => null,
            'status'      => 'pending_manual',
            'signing_url' => null,
        ];
    }

    public function getStatus(string $externalId): array
    {
        return ['external_id' => $externalId, 'status' => 'pending_manual', 'signed_at' => null];
    }

    public function testConnection(): array
    {
        return ['success' => true, 'message' => 'Modo manual — no requiere configuración.'];
    }
}
