<?php

namespace App\Services\Signatures;

use App\Contracts\SignatureProviderInterface;
use App\Models\Setting;
use InvalidArgumentException;

/**
 * Resuelve el driver de firma activo. Proveedores pagos (DocuSign,
 * Signaturit, Dropbox Sign, Zoho Sign, etc.) se agregan a futuro como una
 * clase nueva que implemente SignatureProviderInterface + un case acá.
 */
class SignatureProviderManager
{
    public static function driver(?string $name = null): SignatureProviderInterface
    {
        $name ??= Setting::get('firma_digital.driver', 'self_hosted');

        return match ($name) {
            'self_hosted' => app(SelfHostedSignatureProvider::class),
            'manual'      => app(ManualSignatureProvider::class),
            default => throw new InvalidArgumentException("Driver de firma desconocido: [{$name}]."),
        };
    }
}
