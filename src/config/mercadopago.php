<?php

return [
    'mode'               => env('MERCADOPAGO_MODE', 'test'),
    'public_key_test'    => env('MERCADOPAGO_PUBLIC_KEY_TEST'),
    'access_token_test'  => env('MERCADOPAGO_ACCESS_TOKEN_TEST'),
    'public_key_prod'    => env('MERCADOPAGO_PUBLIC_KEY_PROD'),
    'access_token_prod'  => env('MERCADOPAGO_ACCESS_TOKEN_PROD'),
    'webhook_secret'     => env('MERCADOPAGO_WEBHOOK_SECRET'),
];