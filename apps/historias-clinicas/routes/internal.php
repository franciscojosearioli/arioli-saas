<?php

use App\Http\Controllers\Internal\CatalogoController;
use App\Http\Controllers\Internal\DemoController;
use App\Http\Controllers\Internal\ProvisionController;
use Illuminate\Support\Facades\Route;

Route::post('/demo/seed',  [DemoController::class, 'seed'])->name('internal.demo.seed');
Route::post('/demo/reset', [DemoController::class, 'reset'])->name('internal.demo.reset');

// Etapa 6.5: provisioning de cliente real, llamado por la app central.
Route::post('/provision', [ProvisionController::class, 'store'])->name('internal.provision.store');

// Etapa 7.2: catálogo único, de solo lectura — mismo token que el resto de /internal.
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('internal.catalogo.index');
