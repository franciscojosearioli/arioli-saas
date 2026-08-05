<?php

namespace App\Observers;

use App\Models\Desarrollo;
use App\Services\Marketplace\DesarrolloSync;

class DesarrolloObserver
{
    public function created(Desarrollo $desarrollo): void
    {
        DesarrolloSync::sincronizar($desarrollo);
    }

    public function updated(Desarrollo $desarrollo): void
    {
        DesarrolloSync::sincronizar($desarrollo);
    }
}
