<?php

namespace App\Services\Versioning;

interface VersioningClientInterface
{
    public function installedVersion(): string;
    public function status(): VersionStatus;
    public function requestUpdate(string $tenantId): bool;
}
