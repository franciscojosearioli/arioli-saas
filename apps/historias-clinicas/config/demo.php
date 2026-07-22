<?php

return [
    'protected_user_id' => 1,
    'saas_portal_url'   => env('SAAS_PORTAL_URL', 'http://admin.127.0.0.1.nip.io'),

    // Credenciales visibles en la pantalla de login del entorno demo.
    // Fuente de verdad: estas son las credenciales que genera ProvisionDemoInstance + DemoSeeder.
    'credentials' => [
        ['role' => 'Admin',                  'email' => 'admin@demo.com',            'password' => 'demo1234'],
        ['role' => 'Psicóloga (Panel)',       'email' => 'psicologa@demo.com',        'password' => 'demo1234'],
        ['role' => 'Psiquiatra (Panel)',      'email' => 'psiquiatra@demo.com',       'password' => 'demo1234'],
        ['role' => 'Secretaria (Panel)',      'email' => 'secretaria@demo.com',       'password' => 'demo1234'],
    ],

    'blocked_routes' => [
        // Usuarios (Route::resource('users', ...) genera users.store, users.update, users.destroy)
        'users.store', 'users.update', 'users.destroy',
        // Roles
        'roles.store', 'roles.update', 'roles.destroy',
        // Perfil y contraseña — admin
        'password.update', 'password.updateProfile',
        // Perfil y contraseña — panel operativo
        'profile.update', 'profile.password',
        // Configuración del sistema (nombre, logo, favicon)
        'configuracion.update',

        // Onboarding — las demos nunca ejecutan setup inicial (defensa en profundidad)
        'setup.wizard',
        'setup.save',
    ],
];
