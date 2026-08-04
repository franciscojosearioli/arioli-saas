<?php

namespace App\Services\License;

interface LicenseClientInterface
{
    public function getInfo(): LicenseInfo;

    public function isDemo(): bool;

    public function isActive(): bool;

    public function getType(): string;
}
