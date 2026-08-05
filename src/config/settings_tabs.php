<?php

// Registro de pestañas de la página Configuración. Agregar una pestaña nueva
// (certificados, notificaciones, APIs, storage, backups, IA, etc.) es sumar
// una entrada acá + crear su vista parcial — no requiere tocar rutas,
// controller ni el resto de las pestañas.

return [
    'empresa' => [
        'label' => 'Empresa',
        'view'  => 'admin.settings.tabs.empresa',
    ],
    'mercadopago' => [
        'label' => 'Mercado Pago',
        'view'  => 'admin.settings.tabs.mercadopago',
    ],
    'afip' => [
        'label' => 'Facturación electrónica',
        'view'  => 'admin.settings.tabs.afip',
    ],
    'firma_digital' => [
        'label' => 'Firma Digital',
        'view'  => 'admin.settings.tabs.firma-digital',
    ],
    'correos' => [
        'label' => 'Correos',
        'view'  => 'admin.settings.tabs.correos',
    ],
    'horas' => [
        'label' => 'Horas / Facturación',
        'view'  => 'admin.settings.tabs.horas',
    ],
    'auditoria' => [
        'label'    => 'Auditoría',
        'view'     => 'admin.settings.tabs.auditoria',
        'readonly' => true,
    ],
];
