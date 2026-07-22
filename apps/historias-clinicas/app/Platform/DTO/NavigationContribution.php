<?php

namespace App\Platform\DTO;

use App\Platform\Contracts\Contribution;

final class NavigationContribution implements Contribution
{
    public function __construct(public readonly NavigationItem $item)
    {
    }
}
