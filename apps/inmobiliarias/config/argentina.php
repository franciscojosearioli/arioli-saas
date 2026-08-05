<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Provincias
    |--------------------------------------------------------------------------
    |
    | Las 23 provincias + CABA. Referencia real y completa — no un
    | placeholder — usada tanto por factories/seeders como, más adelante,
    | por los selects de los formularios reales.
    |
    */
    'provincias' => [
        'Buenos Aires',
        'Ciudad Autónoma de Buenos Aires',
        'Catamarca',
        'Chaco',
        'Chubut',
        'Córdoba',
        'Corrientes',
        'Entre Ríos',
        'Formosa',
        'Jujuy',
        'La Pampa',
        'La Rioja',
        'Mendoza',
        'Misiones',
        'Neuquén',
        'Río Negro',
        'Salta',
        'San Juan',
        'San Luis',
        'Santa Cruz',
        'Santa Fe',
        'Santiago del Estero',
        'Tierra del Fuego',
        'Tucumán',
    ],

    /*
    |--------------------------------------------------------------------------
    | Servicios de una propiedad
    |--------------------------------------------------------------------------
    |
    | Lista de referencia para generar datos de ejemplo realistas. La
    | columna real (Propiedad.servicios) es JSON libre, no un enum — un
    | tenant puede cargar cualquier servicio que no esté acá.
    |
    */
    'servicios' => [
        'agua corriente',
        'luz eléctrica',
        'gas natural',
        'gas envasado',
        'cloacas',
        'internet / fibra óptica',
        'pavimento',
        'alumbrado público',
        'transporte público',
        'seguridad privada',
        'tv por cable',
    ],
];
