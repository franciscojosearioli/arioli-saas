<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landlord Routes
|--------------------------------------------------------------------------
|
| Solo lo que corre en el dominio central (sin tenant identificado):
| landing/estado del sistema y provisioning. Todo lo que ve una
| inmobiliaria vive en routes/tenant.php.
|
*/

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'context' => 'landlord',
    ]);
});
