<?php

use App\Http\Controllers\Cliente\AccountUsersController;
use App\Http\Controllers\Cliente\AuthController;
use App\Http\Controllers\Cliente\BillingController;
use App\Http\Controllers\Cliente\DomainController;
use App\Http\Controllers\Cliente\HelpController;
use App\Http\Controllers\Cliente\DashboardController;
use App\Http\Controllers\Cliente\HostingController;
use App\Http\Controllers\Cliente\ServicesController;
use App\Http\Controllers\Cliente\ServiceStatusController;
use App\Http\Controllers\Cliente\SystemsController;
use App\Http\Controllers\Cliente\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/login',  [AuthController::class, 'showLogin'])->name('cliente.login');
Route::post('/login', [AuthController::class, 'login'])->name('cliente.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('cliente.logout');
Route::get('/entrar-como/{user}', [AuthController::class, 'enterAsImpersonated'])->name('cliente.impersonate.enter')->middleware('signed');

    // Password reset
    Route::get('/forgot-password',          [AuthController::class, 'showForgotPassword'])->name('cliente.password.request');
    Route::post('/forgot-password',         [AuthController::class, 'sendResetLink'])->name('cliente.password.email');
    Route::get('/reset-password/{token}',   [AuthController::class, 'showResetPassword'])->name('cliente.password.reset');
    Route::post('/reset-password',          [AuthController::class, 'resetPassword'])->name('cliente.password.update');
    
Route::middleware('auth.cliente')->group(function () {
    Route::get('/',                                  [DashboardController::class, 'index'])->name('cliente.dashboard');

    Route::get('/sistemas',                          [SystemsController::class, 'index'])->name('cliente.systems.index');

    Route::get('/hosting',                           [HostingController::class, 'index'])->name('cliente.hosting.index');
    Route::post('/hosting/{hosting}/reenviar-acceso', [HostingController::class, 'resendAccess'])->name('cliente.hosting.resend-access');

    Route::prefix('dominios')->name('cliente.domains.')->group(function () {
        Route::get('/',                              [DomainController::class, 'index'])->name('index');
        Route::post('/{domain}/renovar',              [DomainController::class, 'renew'])->name('renew');
        Route::post('/{domain}/transferir',           [DomainController::class, 'requestTransfer'])->name('transfer');
        Route::get('/{domain}/dns',                   [DomainController::class, 'dns'])->name('dns');
        Route::post('/{domain}/dns',                  [DomainController::class, 'storeDnsRecord'])->name('dns.store');
        Route::patch('/{domain}/dns/{record}',        [DomainController::class, 'updateDnsRecord'])->name('dns.update');
        Route::delete('/{domain}/dns/{record}',       [DomainController::class, 'destroyDnsRecord'])->name('dns.destroy');
        Route::get('/{domain}/nameservers',            [DomainController::class, 'nameservers'])->name('nameservers');
        Route::post('/{domain}/nameservers',           [DomainController::class, 'updateNameservers'])->name('nameservers.update');
    });

    Route::get('/servicios',                          [ServicesController::class, 'index'])->name('cliente.services.index');

    Route::get('/facturacion',                                [BillingController::class, 'index'])->name('cliente.billing.index');
    Route::get('/facturacion/cobros/{charge}',                 [BillingController::class, 'showCharge'])->name('cliente.billing.charges.show');
    Route::get('/facturacion/facturas/{invoice}',              [BillingController::class, 'printInvoice'])->name('cliente.billing.invoices.show');
    Route::get('/facturacion/presupuestos/{quote}/descargar',  [BillingController::class, 'downloadQuote'])->name('cliente.billing.quotes.download');

    Route::get('/estado-de-servicios', [ServiceStatusController::class, 'index'])->name('cliente.service-status.index');

    Route::get('/mi-cuenta/usuarios', [AccountUsersController::class, 'index'])->name('cliente.account.users.index');
    Route::post('/mi-cuenta/usuarios', [AccountUsersController::class, 'store'])->name('cliente.account.users.store');
    Route::delete('/mi-cuenta/usuarios/{portalUser}', [AccountUsersController::class, 'destroy'])->name('cliente.account.users.destroy');

    Route::get('/centro-de-ayuda', [HelpController::class, 'index'])->name('cliente.help.index');
    Route::get('/centro-de-ayuda/{article:slug}', [HelpController::class, 'show'])->name('cliente.help.show');
    Route::get('/licencias',                         [DashboardController::class, 'licencias'])->name('cliente.licencias');
    Route::get('/licencias/{license}',               [DashboardController::class, 'showLicense'])->name('cliente.licencia.show');
    Route::post('/licencias/{license}/dominio',      [DashboardController::class, 'setCustomDomain'])->name('cliente.licencia.custom-domain');
    Route::post('/licencias/{license}/reset',        [DashboardController::class, 'factoryReset'])->name('cliente.licencia.reset');
    Route::get('/pagos',                             [DashboardController::class, 'pagos'])->name('cliente.pagos');
    Route::get('/perfil',                            [DashboardController::class, 'perfil'])->name('cliente.perfil');
    Route::patch('/perfil',                          [DashboardController::class, 'updatePerfil'])->name('cliente.perfil.update');
    Route::get('/renovar/{license}',                 [DashboardController::class, 'showRenovar'])->name('cliente.renovar');
    Route::post('/renovar/{license}',                [DashboardController::class, 'processRenovar'])->name('cliente.renovar.post');
    Route::post('/baja/{license}',                   [DashboardController::class, 'baja'])->name('cliente.baja');
    Route::post('/orden/{order}/cancelar',           [DashboardController::class, 'cancelOrder'])->name('cliente.order.cancel');

    // Tickets de Soporte
    Route::prefix('tickets')->name('cliente.tickets.')->group(function () {
        Route::get('/',                              [TicketController::class, 'index'])->name('index');
        Route::get('/crear',                         [TicketController::class, 'create'])->name('create');
        Route::post('/',                            [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}',                     [TicketController::class, 'show'])->name('show');
        Route::get('/{ticket}/editar',              [TicketController::class, 'edit'])->name('edit');
        Route::patch('/{ticket}',                   [TicketController::class, 'update'])->name('update');
        Route::patch('/{ticket}/cerrar',            [TicketController::class, 'close'])->name('close');
    });

});