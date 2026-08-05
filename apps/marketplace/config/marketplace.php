<?php

return [
    // Token compartido que cada tenant manda como Bearer al llamar
    // /api/publications — mismo patrón que el api.key de inmobiliarias
    // para /api/provision.
    'api_token' => env('MARKETPLACE_API_TOKEN'),
];
