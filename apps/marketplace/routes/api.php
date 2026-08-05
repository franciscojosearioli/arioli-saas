<?php

use App\Http\Controllers\Api\ConstructoraProfileController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\TenantProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API interna del Marketplace (§08/§09)
|--------------------------------------------------------------------------
|
| La llaman los ChannelAdapter/servicios de sync de cada tenant, nunca un
| navegador — protegida por token compartido, no por sesión de usuario.
| §01: es la ÚNICA forma en que un tenant puede afectar el marketplace,
| nunca escritura directa a esta base.
|
*/

Route::middleware('api.key')->group(function () {
    Route::prefix('publications')->group(function () {
        Route::post('/', [PublicationController::class, 'store']);
        Route::get('/{publication}', [PublicationController::class, 'show']);
        Route::put('/{publication}', [PublicationController::class, 'update']);
        Route::delete('/{publication}', [PublicationController::class, 'destroy']);
    });

    Route::put('tenant-profile', [TenantProfileController::class, 'update']);
    Route::put('constructora-profile', [ConstructoraProfileController::class, 'update']);
});
