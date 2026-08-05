<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\ConstructoraController;
use App\Http\Controllers\Api\V1\DesarrolloController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\PropiedadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Todo lo que ve una inmobiliaria (usuarios, auth, dashboard) vive acá,
| no en routes/web.php — users/roles son per-tenant (§02/§07 del Artifact
| de arquitectura), el landlord solo conoce tenants/domains.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'license',
])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';
});

/*
|--------------------------------------------------------------------------
| API v1 (tenant) — §12: REST versionado, Sanctum, Resources/Policies
|--------------------------------------------------------------------------
|
| Catálogo (Constructora, Desarrollo, Propiedad) y CRM (Cliente, Lead) de
| la Fase 1. Stateless: auth:sanctum en vez de la sesión 'web', misma
| identificación de tenant y licencia que el resto de las rutas.
|
*/
Route::prefix('api/v1')->middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'license',
    'auth:sanctum',
    // Sin 'web' ni 'api' de grupo, el route model binding ({propiedad},
    // {cliente}, etc.) no corre nunca: Laravel solo lo sustituye vía este
    // middleware, no automáticamente por el type-hint del controller.
    SubstituteBindings::class,
])->group(function () {
    Route::apiResource('constructoras', ConstructoraController::class);
    Route::apiResource('desarrollos', DesarrolloController::class);
    // Sin esto, Route::apiResource singulariza "propiedades" al inglés
    // -> {propiedade} (mismo problema que la tabla, ver Propiedad.php).
    Route::apiResource('propiedades', PropiedadController::class)
        ->parameters(['propiedades' => 'propiedad']);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('leads', LeadController::class);
});
