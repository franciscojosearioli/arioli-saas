<?php

namespace App\Platform\Contracts\Services;

interface ComponenteInstallerContract
{
    /** @param string[] $componentKeys */
    public function instalar(array $componentKeys): void;
}
