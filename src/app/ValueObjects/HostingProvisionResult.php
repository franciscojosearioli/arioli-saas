<?php

namespace App\ValueObjects;

/**
 * Resultado de HostingPanelInterface::createAccount() — reemplaza un array
 * con índices mágicos por un objeto tipado, más mantenible.
 */
final class HostingProvisionResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $username = null,
        public readonly ?string $panelUrl = null,
        public readonly ?string $message = null,
    ) {
    }

    public static function success(string $username, ?string $panelUrl = null, ?string $message = null): self
    {
        return new self(success: true, username: $username, panelUrl: $panelUrl, message: $message);
    }

    public static function failure(string $message): self
    {
        return new self(success: false, message: $message);
    }
}
