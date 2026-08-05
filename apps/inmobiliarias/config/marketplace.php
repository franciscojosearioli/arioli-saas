<?php

return [
    // §01: el marketplace es un servicio aparte con su propia API interna
    // — este tenant nunca escribe directo a su base. api_url apunta al
    // nginx compartido (marketplace_app solo habla FastCGI, no HTTP
    // directo) — api_host es el Host header que hace que nginx lo rutee
    // al vhost del marketplace en vez de caer al server block por defecto.
    'api_url' => env('MARKETPLACE_API_URL'),
    'api_host' => env('MARKETPLACE_API_HOST'),

    // Token compartido que identifica a este tenant ante el marketplace —
    // igual patrón que SAAS_API_TOKEN para el core.
    'api_token' => env('MARKETPLACE_API_TOKEN'),
];
