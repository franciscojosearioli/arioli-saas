<?php

return [
    /*
     * Módulos que PlatformServiceProvider registra en PlatformRegistry.
     * Agregar un módulo nuevo es agregar una línea acá — no hace falta
     * tocar PlatformServiceProvider.
     */
    'modules' => [
        \App\Platform\Modules\EspecialidadesModule::class,
        \App\Platform\Modules\AgendaModule::class,
        \App\Platform\Modules\ConsentimientosModule::class,
        \App\Platform\Modules\MedicacionModule::class,
        \App\Platform\Modules\InformesModule::class,
    ],
];
