<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta (Facebook + Instagram) — §09 Fase 4
    |--------------------------------------------------------------------------
    |
    | Una sola app de Meta for Developers, compartida por todos los
    | tenants (cada uno conecta su propia Página/cuenta, no su propia
    | app) — mismo criterio que cualquier integrador multi-tenant real.
    | 'redirect' no se fija acá: cada tenant tiene su propio subdominio,
    | así que el controller arma la URL de vuelta en runtime, no una fija
    | por config.
    |
    */
    'facebook' => [
        'client_id' => env('META_APP_ID'),
        'client_secret' => env('META_APP_SECRET'),
        'graph_version' => env('META_GRAPH_VERSION', 'v19.0'),
    ],

];
