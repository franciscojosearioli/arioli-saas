<?php

namespace App\ValueObjects;

/**
 * Resultado genérico de una operación sobre HostingPanelInterface
 * (getUsage/suspend/changePassword/testConnection) — `data` es un array
 * libre para lo específico de cada operación (ej. números de uso).
 */
final class HostingOperationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly array $data = [],
    ) {
    }

    public static function success(?string $message = null, array $data = []): self
    {
        return new self(success: true, message: $message, data: $data);
    }

    public static function failure(string $message): self
    {
        return new self(success: false, message: $message);
    }
}
