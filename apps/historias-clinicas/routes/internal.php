<?php

use App\Http\Controllers\Internal\DemoController;
use Illuminate\Support\Facades\Route;

Route::post('/demo/seed',  [DemoController::class, 'seed'])->name('internal.demo.seed');
Route::post('/demo/reset', [DemoController::class, 'reset'])->name('internal.demo.reset');
