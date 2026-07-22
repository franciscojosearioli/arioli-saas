<?php

namespace App\Platform\Contracts\Services;

use App\Models\User;

interface NavigationInstallerContract
{
    /** @return \App\Platform\DTO\NavigationItem[] */
    public function resolverPara(User $user): array;
}
