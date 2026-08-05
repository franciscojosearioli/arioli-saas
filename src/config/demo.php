<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo credentials per product slug
    |--------------------------------------------------------------------------
    | Used by the SaaS Core admin panel (license show view) to display
    | access credentials when a license is of type 'demo'.
    */

    'credentials' => [

        'loteos' => [
            ['role' => 'Admin',   'email' => 'admin@demo.com',            'password' => 'demo1234'],
            ['role' => 'Cliente', 'email' => 'maria.garcia@demo.com',     'password' => 'demo1234'],
            ['role' => 'Cliente', 'email' => 'carlos.martinez@demo.com',  'password' => 'demo1234'],
        ],

        'tallerpro' => [
            ['role' => 'Admin', 'email' => 'admin@demo.com', 'password' => 'demo1234'],
        ],

        'historias-clinicas' => [
            ['role' => 'Admin',             'email' => 'admin@demo.com',       'password' => 'demo1234'],
            ['role' => 'Psicóloga',         'email' => 'psicologa@demo.com',   'password' => 'demo1234'],
            ['role' => 'Psiquiatra',        'email' => 'psiquiatra@demo.com',  'password' => 'demo1234'],
            ['role' => 'Secretaria',        'email' => 'secretaria@demo.com',  'password' => 'demo1234'],
        ],

    ],

];
