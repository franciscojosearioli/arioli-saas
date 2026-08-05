<?php

namespace App\Observers;

use App\Models\Constructora;
use App\Services\Marketplace\PerfilConstructoraSync;

class ConstructoraObserver
{
    public function created(Constructora $constructora): void
    {
        PerfilConstructoraSync::sincronizar($constructora);
    }

    public function updated(Constructora $constructora): void
    {
        PerfilConstructoraSync::sincronizar($constructora);
    }
}
