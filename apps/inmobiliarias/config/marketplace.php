<?php

return [
    // §01: el marketplace es un servicio aparte con su propia API interna
    // — este tenant nunca escribe directo a su base. Dentro de Docker
    // apunta al service name del contenedor (ver docker-compose.yml).
    'api_url' => env('MARKETPLACE_API_URL'),

    // Token compartido que identifica a este tenant ante el marketplace —
    // igual patrón que SAAS_API_TOKEN para el core.
    'api_token' => env('MARKETPLACE_API_TOKEN'),
];
