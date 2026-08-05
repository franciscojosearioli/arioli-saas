<?php

use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\DesarrolloController;
use App\Http\Controllers\FichaController;
use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BusquedaController::class, 'index'])->name('busqueda');
Route::get('/propiedades/{ficha:slug}', [FichaController::class, 'show'])->name('fichas.show');
Route::get('/desarrollos/{desarrollo:slug}', [DesarrolloController::class, 'show'])->name('desarrollos.show');
Route::get('/inmobiliarias/{perfil:slug}', [PerfilController::class, 'inmobiliaria'])->name('perfiles.inmobiliaria');
Route::get('/constructoras/{perfil:slug}', [PerfilController::class, 'constructora'])->name('perfiles.constructora');
