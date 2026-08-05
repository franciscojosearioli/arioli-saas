<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Porkbun — API de registro de dominios
    |--------------------------------------------------------------------------
    |
    | Credenciales de infraestructura (no cambian en runtime) — mismo criterio
    | que HestiaCP en config/hosting_panel.php: NO pasa por Setting::get().
    |
    */

    'api_key'    => env('PORKBUN_API_KEY'),
    'secret_key' => env('PORKBUN_SECRET_KEY'),
    'base_url'   => env('PORKBUN_BASE_URL', 'https://api.porkbun.com/api/json/v3'),
];
