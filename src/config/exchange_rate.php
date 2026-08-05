<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cotización USD/ARS — fallback
    |--------------------------------------------------------------------------
    |
    | DolarRateService usa dolarapi.com (gratis, sin auth) como fuente
    | principal. Este valor solo se usa si esa API falla — se loguea
    | explícitamente como fallback, nunca se usa en silencio.
    |
    */

    'fallback_usd_ars' => env('DOLAR_FALLBACK_RATE', 1000),
];
