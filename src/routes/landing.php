<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = \App\Models\Product::where('active', true)
        ->with(['plans' => fn($q) => $q->where('active', true)->where('price', '>', 0)->where('period_months', '>', 0)->orderBy('period_months')])
        ->get();

    $successCases = \App\Models\Client::showcase()->get();

    return view('landing.index', compact('products', 'successCases'));
})->name('landing.home');

// Casos de éxito
Route::get('/clientes/{client:slug}', function (\App\Models\Client $client) {
    abort_unless($client->show_on_landing, 404);

    $client->load(['projects' => fn($q) => $q->public()->with('images')]);

    return view('landing.caso-exito', compact('client'));
})->name('landing.caso-exito');

Route::get('/productos/{slug}', function (string $slug) {
    $product = \App\Models\Product::where('slug', $slug)
        ->where('active', true)
        ->with(['plans' => fn($q) => $q->where('active', true)->where('price', '>', 0)->where('period_months', '>', 0)->orderBy('period_months')])
        ->firstOrFail();

    $plans   = $product->plans;
    $demoUrl = 'http://demo.' . $product->public_domain . '.' . config('app.tenant_domain');

    return view('landing.product', compact('product', 'plans', 'demoUrl'));
})->name('landing.product');

// Contacto y partners
Route::get('/contacto', [ContactController::class, 'showContact'])->name('landing.contact');
Route::post('/contacto', [ContactController::class, 'sendContact'])->name('landing.contact.send');

Route::get('/ser-partner', [ContactController::class, 'showPartner'])->name('landing.partner');
Route::post('/ser-partner', [ContactController::class, 'sendPartner'])->name('landing.partner.send');

Route::post('/productos/{slug}/consulta', [ContactController::class, 'productInquiry'])->name('landing.product.inquiry');

// Servicios (desarrollo web, desarrollos a medida)
Route::get('/servicios/{slug}', function (string $slug) {
    abort_unless(in_array($slug, ['desarrollo-web', 'desarrollo-a-medida']), 404);
    return view('landing.servicio', ['slug' => $slug]);
})->name('landing.service');

Route::post('/servicios/{slug}/consulta', [ContactController::class, 'serviceInquiry'])->name('landing.service.inquiry');

// Resultados de pago y otras rutas fijas — DEBEN ir antes que {plan}
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
Route::get('/checkout/pending', [CheckoutController::class, 'pending'])->name('checkout.pending');
Route::post('/checkout/verificar-email', [CheckoutController::class, 'verifyEmail'])
    ->middleware('throttle:10,1')
    ->name('checkout.verify-email');

// Checkout con plan
Route::get('/checkout/{plan}',  [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/{plan}', [CheckoutController::class, 'process'])->name('checkout.process');

// Webhook
Route::post('/webhook/mercadopago', [CheckoutController::class, 'webhook'])->name('checkout.webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Webhook de Cobros del CRM (separado del de Checkout — Órdenes de licencias)
Route::post('/webhook/mercadopago/cobros', [\App\Http\Controllers\ChargeWebhookController::class, 'handle'])->name('charges.webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Firma electrónica autogestionada — público, sin login, protegido por el token en sí
Route::get('/firmar/{token}',  [SignatureController::class, 'show'])->middleware('throttle:20,1')->name('signature.show');
Route::post('/firmar/{token}', [SignatureController::class, 'sign'])->middleware('throttle:20,1')->name('signature.sign');
Route::post('/firmar/{token}/rechazar', [SignatureController::class, 'reject'])->middleware('throttle:20,1')->name('signature.reject');

// Cotizaciones — aceptar/rechazar público, sin login, protegido por el token en sí
Route::get('/cotizaciones/{token}',  [QuoteController::class, 'show'])->middleware('throttle:20,1')->name('quotes.public.show');
Route::get('/cotizaciones/{token}/descargar', [QuoteController::class, 'download'])->middleware('throttle:20,1')->name('quotes.public.download');
Route::post('/cotizaciones/{token}/aceptar',  [QuoteController::class, 'accept'])->middleware('throttle:20,1')->name('quotes.public.accept');
Route::post('/cotizaciones/{token}/rechazar', [QuoteController::class, 'reject'])->middleware('throttle:20,1')->name('quotes.public.reject');

// Credenciales de hosting — link firmado, el cliente define su propia contraseña
// (la orden de hosting en sí NO es pública — la crea el admin desde la ficha del
// cliente, ver clients/{client}/hosting-orders en routes/web.php)
Route::get('/hosting/{account}/credenciales', [\App\Http\Controllers\HostingCredentialController::class, 'show'])->name('hosting.credentials.show');
Route::post('/hosting/{account}/credenciales', [\App\Http\Controllers\HostingCredentialController::class, 'claim'])->name('hosting.credentials.claim');

Route::get('/mantenimiento/{service}/confirmar', [\App\Http\Controllers\MaintenanceConfirmationController::class, 'confirm'])->name('maintenance.confirm')->middleware('signed');
Route::get('/mantenimiento/{service}/backup', [\App\Http\Controllers\MaintenanceConfirmationController::class, 'downloadBackup'])->name('maintenance.download-backup')->middleware('signed');