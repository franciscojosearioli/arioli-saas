<?php

use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\LicenseInfoController;
use App\Http\Controllers\Api\ProvisioningController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::get('/license/validate', [LicenseController::class, 'validate']);
    Route::get('/license/info',     [LicenseInfoController::class, 'show']);
    Route::post('/provision/loteos', [ProvisioningController::class, 'provisionLoteos']);

    Route::prefix('app')->group(function () {
        Route::get('/version',            [AppVersionController::class, 'latest']);
        Route::post('/version/installed', [AppVersionController::class, 'reportInstalled']);
        Route::post('/update',            [AppVersionController::class, 'requestUpdate']);
    });
});
