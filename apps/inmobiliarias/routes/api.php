<?php

use App\Http\Controllers\Api\ProvisionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API central (landlord)
|--------------------------------------------------------------------------
|
| Llamada por el core de Arioli (SAAS_API_URL) para provisionar tenants
| nuevos — protegida por token compartido (config('saas.api_token')),
| nunca por el dominio del tenant.
|
*/

Route::middleware('api.key')->post('/provision', [ProvisionController::class, 'provision']);
