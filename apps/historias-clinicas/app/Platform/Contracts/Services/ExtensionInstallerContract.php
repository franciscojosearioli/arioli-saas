<?php

namespace App\Platform\Contracts\Services;

use App\Platform\Contracts\ComponenteExtension;

interface ExtensionInstallerContract
{
    public function aplicar(string $componenteKey, ComponenteExtension $extension): void;
}
