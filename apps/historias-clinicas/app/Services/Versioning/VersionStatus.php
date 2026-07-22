<?php

namespace App\Services\Versioning;

final class VersionStatus
{
    public function __construct(
        public readonly string  $currentVersion,
        public readonly ?string $latestVersion,
        public readonly bool    $updateAvailable,
        public readonly bool    $apiReachable,
    ) {}
}
