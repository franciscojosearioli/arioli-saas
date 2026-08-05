<?php

use App\Http\Controllers\Api\PublicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API interna del Marketplace (§08/§09)
|--------------------------------------------------------------------------
|
| La llaman los ChannelAdapter de cada tenant (MarketplacePropioAdapter),
| nunca un navegador — protegida por token compartido, no por sesión de
| usuario. §01: es la ÚNICA forma en que un tenant puede afectar el
| marketplace, nunca escritura directa a esta base.
|
*/

Route::middleware('api.key')->prefix('publications')->group(function () {
    Route::post('/', [PublicationController::class, 'store']);
    Route::get('/{publication}', [PublicationController::class, 'show']);
    Route::put('/{publication}', [PublicationController::class, 'update']);
    Route::delete('/{publication}', [PublicationController::class, 'destroy']);
});
