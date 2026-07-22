<?php

namespace App\Platform\Contracts\Services;

interface FieldVisibilityInstallerContract
{
    /** @param \App\Platform\DTO\FieldVisibilitySeed[] $seeds */
    public function aplicar(array $seeds): void;
}
