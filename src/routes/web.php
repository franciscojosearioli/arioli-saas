<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Application
|--------------------------------------------------------------------------
| admin.127.0.0.1.nip.io
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('admin.landing');
})->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Tenants
    |--------------------------------------------------------------------------
    */

    Route::prefix('tenants')->name('tenants.')->group(function () {

        Route::get('/', function () {
            return view('admin.tenants.index');
        })->name('index');

    });

    /*
    |--------------------------------------------------------------------------
    | Licenses
    |--------------------------------------------------------------------------
    */

    Route::prefix('licenses')->name('licenses.')->group(function () {

        Route::get('/', function () {
            return view('admin.licenses.index');
        })->name('index');

    });

});

require __DIR__.'/auth.php';