<?php

namespace App\Services\License;

final class LicenseInfo
{
    public function __construct(
        public readonly string $product,
        public readonly string $plan,
        public readonly string $licenseType,
        public readonly bool $active,
        public readonly ?string $startsAt,
        public readonly ?string $expiresAt,
        public readonly ?int $daysRemaining,
        public readonly string $domain,
        public readonly string $installedVersion,
        public readonly string $latestVersion,
        public readonly string $lastValidatedAt,
    ) {}

    public function isDemo(): bool
    {
        return $this->licenseType === 'demo';
    }

    public function isCommercial(): bool
    {
        return $this->licenseType === 'commercial';
    }

    public function isTrial(): bool
    {
        return $this->licenseType === 'trial';
    }

    public function isLifetime(): bool
    {
        return $this->licenseType === 'lifetime';
    }

    public function typeLabel(): string
    {
        return match ($this->licenseType) {
            'demo' => 'DEMO',
            'trial' => 'PRUEBA',
            'internal' => 'INTERNO',
            'lifetime' => 'VITALICIA',
            default => 'COMERCIAL',
        };
    }
}
