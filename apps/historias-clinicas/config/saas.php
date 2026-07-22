<?php

return [
    'api_url'     => env('SAAS_API_URL'),
    'version_url' => env('SAAS_VERSION_URL', 'http://nginx/api/app/version'),
    'update_url'  => env('SAAS_UPDATE_URL',  'http://nginx/api/app/update'),
    'info_url'    => env('SAAS_INFO_URL',    'http://nginx/api/license/info'),
    'admin_host'  => env('SAAS_ADMIN_HOST', 'admin.127.0.0.1.nip.io'),
    'api_token'   => env('SAAS_API_TOKEN'),
    'portal_url'  => env('SAAS_PORTAL_URL', 'http://admin.127.0.0.1.nip.io'),
    'client_url'  => env('SAAS_CLIENT_URL', 'http://cliente.127.0.0.1.nip.io'),
    'landing_url' => env('SAAS_LANDING_URL', 'http://127.0.0.1.nip.io'),
];