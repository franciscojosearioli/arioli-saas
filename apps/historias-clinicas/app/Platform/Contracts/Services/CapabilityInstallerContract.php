<?php

namespace App\Platform\Contracts\Services;

interface CapabilityInstallerContract
{
    /** @param string[] $capabilityKeys */
    public function aplicar(array $capabilityKeys): void;
}
