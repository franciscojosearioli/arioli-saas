<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ArqueoCajaController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\ComisionController;
use App\Http\Controllers\Api\V1\ConfiguracionController;
use App\Http\Controllers\Api\V1\ConstructoraController;
use App\Http\Controllers\Api\V1\ContratoController;
use App\Http\Controllers\Api\V1\CuotaController;
use App\Http\Controllers\Api\V1\DesarrolloController;
use App\Http\Controllers\Api\V1\DocumentoController;
use App\Http\Controllers\Api\V1\LeadController;
use App\Http\Controllers\Api\V1\OperacionController;
use App\Http\Controllers\Api\V1\PagoController;
use App\Http\Controllers\Api\V1\PropiedadController;
use App\Http\Controllers\Api\V1\PublicacionController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Catalogo\Constructoras;
use App\Livewire\Catalogo\Desarrollos;
use App\Livewire\Catalogo\Propiedades;
use App\Livewire\Configuracion as ConfiguracionPage;
use App\Livewire\Crm\Clientes;
use App\Livewire\Crm\Leads;
use App\Livewire\Finanzas\Caja;
use App\Livewire\Finanzas\Cobranza;
use App\Livewire\Finanzas\Comisiones;
use App\Livewire\Operaciones\Index as OperacionesIndex;
use App\Livewire\Operaciones\Show as OperacionesShow;
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
    // Sin landing pública propia (§08: eso lo resuelve el marketplace) —
    // el subdominio de un tenant es un panel privado, no una página de
    // marketing. Antes servía el welcome.blade.php de scaffold de
    // Breeze sin reemplazar, nunca corregido desde Fase 0.
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Panel de Catálogo + CRM (§04) — la autorización real vive en
        // cada Policy, no acá: estas rutas solo exigen estar logueado.
        // Prefijo 'panel.' en el NOMBRE (no en la URI): Route::apiResource
        // en api/v1 genera 'constructoras.index' etc. para el mismo
        // recurso — sin este prefijo, route:cache falla en producción por
        // nombres de ruta duplicados (no se detecta en dev sin cachear).
        Route::get('/constructoras', Constructoras::class)->name('panel.constructoras.index');
        Route::get('/desarrollos', Desarrollos::class)->name('panel.desarrollos.index');
        Route::get('/propiedades', Propiedades::class)->name('panel.propiedades.index');
        Route::get('/clientes', Clientes::class)->name('panel.clientes.index');
        Route::get('/leads', Leads::class)->name('panel.leads.index');

        // Fase 2 — Operaciones + Finanzas (§04, §17 Rev. 1.2).
        Route::get('/operaciones', OperacionesIndex::class)->name('panel.operaciones.index');
        Route::get('/operaciones/{operacion}', OperacionesShow::class)->name('panel.operaciones.show');
        Route::get('/cobranza', Cobranza::class)->name('cobranza');
        Route::get('/comisiones', Comisiones::class)->name('comisiones');
        Route::get('/caja', Caja::class)->name('caja');
        Route::get('/configuracion', ConfiguracionPage::class)->name('configuracion');
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

    /*
    |----------------------------------------------------------------
    | Fase 2 — Operaciones + Finanzas (§04, §17 Rev. 1.2)
    |----------------------------------------------------------------
    */
    // Mismo problema de pluralización inglesa que 'propiedades' -> ver
    // Propiedad.php / Operacion.php.
    Route::apiResource('operaciones', OperacionController::class)
        ->parameters(['operaciones' => 'operacion']);
    Route::post('operaciones/{operacion}/partes', [OperacionController::class, 'asignarParte']);
    Route::post('operaciones/{operacion}/plan-de-cuotas', [OperacionController::class, 'generarPlanDeCuotas']);
    Route::post('operaciones/{operacion}/cerrar', [OperacionController::class, 'cerrar']);
    Route::post('operaciones/{operacion}/cancelar', [OperacionController::class, 'cancelar']);

    Route::apiResource('contratos', ContratoController::class);
    Route::post('contratos/{contrato}/renovar', [ContratoController::class, 'renovar']);

    Route::apiResource('cuotas', CuotaController::class);

    // Un pago registrado no se edita ni se borra (PagoPolicy) — solo
    // index/store/show tienen sentido como rutas.
    Route::apiResource('pagos', PagoController::class)->only(['index', 'store', 'show']);

    // La comisión se genera sola al cerrar una Operación — solo lectura +
    // la acción explícita de liquidar, nunca un store/update genérico.
    Route::apiResource('comisiones', ComisionController::class)
        ->only(['index', 'show'])
        ->parameters(['comisiones' => 'comision']);
    Route::post('comisiones/{comision}/liquidar', [ComisionController::class, 'liquidar']);

    Route::apiResource('documentos', DocumentoController::class);

    // Un arqueo ya cerrado no se corrige por API (ArqueoCajaPolicy).
    Route::apiResource('arqueos-caja', ArqueoCajaController::class)
        ->only(['index', 'store', 'show'])
        ->parameters(['arqueos-caja' => 'arqueoCaja']);

    // Fila única de configuración del tenant — no es una colección.
    Route::get('configuracion', [ConfiguracionController::class, 'show']);
    Route::patch('configuracion', [ConfiguracionController::class, 'update']);

    /*
    |----------------------------------------------------------------
    | Fase 3 — Motor de Publicaciones (§08, §09)
    |----------------------------------------------------------------
    */
    Route::post('propiedades/{propiedad}/publicacion', [PublicacionController::class, 'store']);
    Route::get('publicaciones/{publicacion}', [PublicacionController::class, 'show']);
    Route::patch('publicaciones/{publicacion}', [PublicacionController::class, 'update']);
    Route::post('publicaciones/{publicacion}/canales', [PublicacionController::class, 'activarCanal']);
    Route::post('publicacion-canales/{publicacionCanal}/pausar', [PublicacionController::class, 'pausarCanal']);
    Route::post('publicacion-canales/{publicacionCanal}/despublicar', [PublicacionController::class, 'despublicarCanal']);
    Route::post('publicacion-canales/{publicacionCanal}/reintentar', [PublicacionController::class, 'reintentarCanal']);
});
