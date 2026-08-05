<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HestiaCP — conexión SSH
    |--------------------------------------------------------------------------
    |
    | Credenciales de infraestructura (no cambian en runtime) — a diferencia
    | de Mercado Pago/AFIP/Firma Digital, esto NO pasa por Setting::get().
    | Si algún día hace falta editarlas sin deploy, se migra a Setting recién
    | ahí, no antes.
    |
    */

    'hestiacp' => [
        'host'        => env('HESTIACP_SSH_HOST', 'host.arioli.dev'),
        'port'        => env('HESTIACP_SSH_PORT', 22),
        'user'        => env('HESTIACP_SSH_USER', 'ubuntu'),
        'private_key' => env('HESTIACP_SSH_KEY_PATH'),
        'panel_url'   => env('HESTIACP_PANEL_URL', 'https://host.arioli.dev:8083'),
        'domain'      => env('HESTIACP_HESTIA_USER', 'hestiaadmin'), // dueño de los web-domains creados
        // IP pública real del server — la que hay que darle al cliente (o cargar
        // en Porkbun) para el registro A que apunta el dominio a este hosting.
        // "host.arioli.dev" es el hostname de conexión SSH, no sirve como
        // target de un registro A en la zona DNS del cliente.
        'server_ip'   => env('HESTIACP_SERVER_IP', '51.222.138.208'),
    ],
];
