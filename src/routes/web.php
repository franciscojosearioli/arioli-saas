<?php

use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $totalTenants     = \App\Models\Tenant::where('id', '!=', 'demo')->count();
        $totalLicenses    = \App\Models\License::notDemo()->count();
        $activeLicenses   = \App\Models\License::notDemo()->where('active', true)
                            ->where('expires_at', '>=', now())->count();
        $expiredLicenses  = \App\Models\License::notDemo()->where('expires_at', '<', now())->count();
        $expiringLicenses = \App\Models\License::notDemo()->where('active', true)
                            ->where('expires_at', '>=', now())
                            ->where('expires_at', '<=', now()->addDays(30))->count();
        $totalProducts    = \App\Models\Product::where('active', true)->count();
        $monthlyNewTenants = \App\Models\Tenant::where('id', '!=', 'demo')
                            ->where('created_at', '>=', now()->startOfMonth())->count();

        // Clientes: total de negocio, sin importar si vienen de un tenant con
        // licencia SaaS o si son un Client cargado a mano (CRM de mantenimiento)
        // — la distinción de "cuáles son de qué sistema" queda para más
        // adelante, acá interesa el número total del negocio.
        $totalClientsCrm  = \App\Models\Client::count();
        $monthlyNewClientsCrm = \App\Models\Client::where('created_at', '>=', now()->startOfMonth())->count();
        $totalClientes    = $totalTenants + $totalClientsCrm;
        $monthlyNew       = $monthlyNewTenants + $monthlyNewClientsCrm;

        // Ingresos por licencias SaaS (Order) — ver Finanzas más abajo para lo
        // cobrado a clientes de mantenimiento (Charge/ChargePayment), que es
        // una fuente de plata distinta y no se mezcla en este número.
        $totalRevenue     = \App\Models\Order::where('status', 'approved')->sum('amount');
        $monthlyRevenue   = \App\Models\Order::where('status', 'approved')
                            ->where('created_at', '>=', now()->startOfMonth())->sum('amount');

        // Finanzas — cobros a clientes de mantenimiento/desarrollo (Charge),
        // usando los mismos agregados balance-aware que dashboard/clientes.
        $chargeFinancials   = new \App\Services\Dashboard\ChargeDashboardProvider();
        $paidThisMonth      = $chargeFinancials->formatByCurrency($chargeFinancials->paidThisMonthByCurrency());
        $pendingBalance     = $chargeFinancials->formatByCurrency($chargeFinancials->pendingBalanceByCurrency());
        $totalCollected     = $chargeFinancials->formatByCurrency($chargeFinancials->paidTotalByCurrency());
        $overdueCharges     = \App\Models\Charge::where('status', 'pending')->whereDate('due_date', '<', now())->count();

        // Estadísticas de tickets — solo lo que necesita atención (abiertos y
        // críticos); el histórico total/resueltos vive en /tickets, no acá.
        $openTickets      = \App\Models\Ticket::whereIn('status', ['abierto', 'en_progreso', 'esperando_cliente'])->count();
        $criticalTickets  = \App\Models\Ticket::where('priority', 'critica')->whereNotIn('status', ['resuelto', 'cerrado'])->count();

        // Clientes recientes: mismo criterio de unificación que el KPI de arriba
        // — un tenant con licencia SaaS y un Client cargado a mano son ambos
        // "un cliente", sin importar el sistema de origen.
        $recentClients = collect(
                \App\Models\Tenant::where('id', '!=', 'demo')->get()
                    ->map(fn ($t) => (object) [
                        'name'       => $t->name ?? $t->id,
                        'created_at' => $t->created_at,
                        'url'        => route('tenants.show', $t->id),
                    ])
            )
            ->merge(
                \App\Models\Client::all()->map(fn ($c) => (object) [
                    'name'       => $c->name,
                    'created_at' => $c->created_at,
                    'url'        => route('clients.show', $c->id),
                ])
            )
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // Próximas a vencer: todo el negocio (dominios, hosting, SSL,
        // Cloudflare y licencias), no solo licencias.
        $upcomingRenewalsList = (new \App\Services\Dashboard\UpcomingRenewalsDashboardService())->calculate();

        return view('admin.dashboard', compact(
            'totalTenants', 'totalLicenses', 'activeLicenses',
            'expiredLicenses', 'expiringLicenses', 'totalProducts',
            'monthlyNew', 'totalClientes', 'recentClients',
            'upcomingRenewalsList', 'totalRevenue', 'monthlyRevenue',
            'paidThisMonth', 'pendingBalance', 'totalCollected', 'overdueCharges',
            'openTickets', 'criticalTickets'
        ));
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Tenants
    |--------------------------------------------------------------------------
    */

    Route::resource('tenants', TenantController::class);

    /*
    |--------------------------------------------------------------------------
    | Clientes (CRM)
    |--------------------------------------------------------------------------
    */

    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class);
    Route::post('clients/{client}/entrar-como-cliente', [\App\Http\Controllers\Admin\ClientController::class, 'impersonate'])->name('clients.impersonate');

    Route::prefix('clients/{client}')->name('clients.')->group(function () {
        Route::post('contacts', [\App\Http\Controllers\Admin\ClientContactController::class, 'store'])->name('contacts.store');
        Route::patch('contacts/{contact}', [\App\Http\Controllers\Admin\ClientContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{contact}', [\App\Http\Controllers\Admin\ClientContactController::class, 'destroy'])->name('contacts.destroy');

        Route::post('domains', [\App\Http\Controllers\Admin\ClientDomainController::class, 'store'])->name('domains.store');
        Route::patch('domains/{domain}', [\App\Http\Controllers\Admin\ClientDomainController::class, 'update'])->name('domains.update');
        Route::delete('domains/{domain}', [\App\Http\Controllers\Admin\ClientDomainController::class, 'destroy'])->name('domains.destroy');

        Route::get('domain-registrations/create', [\App\Http\Controllers\Admin\DomainRegistrationController::class, 'create'])->name('domain-registrations.create');
        Route::post('domain-registrations/check', [\App\Http\Controllers\Admin\DomainRegistrationController::class, 'check'])->name('domain-registrations.check');
        Route::post('domain-registrations', [\App\Http\Controllers\Admin\DomainRegistrationController::class, 'store'])->name('domain-registrations.store');

        Route::post('hostings', [\App\Http\Controllers\Admin\HostingController::class, 'store'])->name('hostings.store');
        Route::patch('hostings/{hosting}', [\App\Http\Controllers\Admin\HostingController::class, 'update'])->name('hostings.update');
        Route::delete('hostings/{hosting}', [\App\Http\Controllers\Admin\HostingController::class, 'destroy'])->name('hostings.destroy');
        Route::post('hostings/{hosting}/apuntar-dns', [\App\Http\Controllers\Admin\HostingController::class, 'pointDns'])->name('hostings.point-dns');
        Route::post('hostings/{hosting}/configurar-correo', [\App\Http\Controllers\Admin\HostingController::class, 'setupMail'])->name('hostings.setup-mail');
        Route::post('hostings/{hosting}/reintentar-ssl', [\App\Http\Controllers\Admin\HostingController::class, 'retrySsl'])->name('hostings.retry-ssl');

        Route::get('hosting-orders/create', [\App\Http\Controllers\Admin\HostingOrderController::class, 'create'])->name('hosting-orders.create');
        Route::post('hosting-orders', [\App\Http\Controllers\Admin\HostingOrderController::class, 'store'])->name('hosting-orders.store');

        Route::post('ssl-certificates', [\App\Http\Controllers\Admin\SslCertificateController::class, 'store'])->name('ssl-certificates.store');
        Route::patch('ssl-certificates/{sslCertificate}', [\App\Http\Controllers\Admin\SslCertificateController::class, 'update'])->name('ssl-certificates.update');
        Route::delete('ssl-certificates/{sslCertificate}', [\App\Http\Controllers\Admin\SslCertificateController::class, 'destroy'])->name('ssl-certificates.destroy');

        Route::post('cloudflare-services', [\App\Http\Controllers\Admin\CloudflareServiceController::class, 'store'])->name('cloudflare-services.store');
        Route::patch('cloudflare-services/{cloudflareService}', [\App\Http\Controllers\Admin\CloudflareServiceController::class, 'update'])->name('cloudflare-services.update');
        Route::delete('cloudflare-services/{cloudflareService}', [\App\Http\Controllers\Admin\CloudflareServiceController::class, 'destroy'])->name('cloudflare-services.destroy');

        Route::post('projects', [\App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'show'])->name('projects.show');
        Route::patch('projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [\App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('projects/{project}/technologies', [\App\Http\Controllers\Admin\ProjectController::class, 'syncTechnologies'])->name('projects.technologies');
        Route::post('projects/{project}/repositories', [\App\Http\Controllers\Admin\ProjectRepositoryController::class, 'store'])->name('projects.repositories.store');
        Route::delete('projects/{project}/repositories/{repository}', [\App\Http\Controllers\Admin\ProjectRepositoryController::class, 'destroy'])->name('projects.repositories.destroy');

        Route::post('projects/{project}/images', [\App\Http\Controllers\Admin\ProjectImageController::class, 'store'])->name('projects.images.store');
        Route::delete('projects/{project}/images/{image}', [\App\Http\Controllers\Admin\ProjectImageController::class, 'destroy'])->name('projects.images.destroy');

        Route::post('projects/{project}/tasks', [\App\Http\Controllers\Admin\ProjectTaskController::class, 'store'])->name('projects.tasks.store');
        Route::patch('projects/{project}/tasks/{task}', [\App\Http\Controllers\Admin\ProjectTaskController::class, 'update'])->name('projects.tasks.update');
        Route::patch('projects/{project}/tasks/{task}/status', [\App\Http\Controllers\Admin\ProjectTaskController::class, 'updateStatus'])->name('projects.tasks.update-status');
        Route::delete('projects/{project}/tasks/{task}', [\App\Http\Controllers\Admin\ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy');

        Route::post('services', [\App\Http\Controllers\Admin\ClientServiceController::class, 'store'])->name('services.store');
        Route::patch('services/{service}', [\App\Http\Controllers\Admin\ClientServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [\App\Http\Controllers\Admin\ClientServiceController::class, 'destroy'])->name('services.destroy');

        Route::post('jobs', [\App\Http\Controllers\Admin\ClientJobController::class, 'store'])->name('jobs.store');
        Route::patch('jobs/{job}', [\App\Http\Controllers\Admin\ClientJobController::class, 'update'])->name('jobs.update');
        Route::delete('jobs/{job}', [\App\Http\Controllers\Admin\ClientJobController::class, 'destroy'])->name('jobs.destroy');

        Route::post('charges', [\App\Http\Controllers\Admin\ChargeController::class, 'store'])->name('charges.store');
        Route::post('charges/{charge}/marcar-pagado', [\App\Http\Controllers\Admin\ChargeController::class, 'markPaid'])->name('charges.mark-paid');
        Route::post('charges/{charge}/pagos', [\App\Http\Controllers\Admin\ChargeController::class, 'storePayment'])->name('charges.payments.store');
        Route::delete('charges/{charge}/pagos/{payment}', [\App\Http\Controllers\Admin\ChargeController::class, 'destroyPayment'])->name('charges.payments.destroy');
        Route::post('charges/{charge}/generar-link', [\App\Http\Controllers\Admin\ChargeController::class, 'regeneratePaymentLink'])->name('charges.regenerate-link');
        Route::delete('charges/{charge}', [\App\Http\Controllers\Admin\ChargeController::class, 'destroy'])->name('charges.destroy');
        Route::post('charges/{charge}/facturar', [\App\Http\Controllers\Admin\ChargeInvoiceController::class, 'store'])->name('charges.invoice.store');

        Route::post('payment-order', [\App\Http\Controllers\Admin\PaymentOrderController::class, 'store'])->name('payment-order.store');

        Route::post('tags', [\App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store');
        Route::delete('tags/{tag}', [\App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.destroy');

        Route::post('tasks', [\App\Http\Controllers\Admin\TaskController::class, 'store'])->name('tasks.store');
        Route::patch('tasks/{task}/toggle', [\App\Http\Controllers\Admin\TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::delete('tasks/{task}', [\App\Http\Controllers\Admin\TaskController::class, 'destroy'])->name('tasks.destroy');

        Route::post('portal-user', [\App\Http\Controllers\Admin\ClientPortalUserController::class, 'store'])->name('portal-user.store');
        Route::post('portal-user/{user}/reenviar', [\App\Http\Controllers\Admin\ClientPortalUserController::class, 'resend'])->name('portal-user.resend');
        Route::delete('portal-user/{user}', [\App\Http\Controllers\Admin\ClientPortalUserController::class, 'destroy'])->name('portal-user.destroy');
    });

    Route::post('notes', [\App\Http\Controllers\Admin\NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{note}', [\App\Http\Controllers\Admin\NoteController::class, 'destroy'])->name('notes.destroy');

    Route::post('documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/descargar', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{document}', [\App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::post('credentials', [\App\Http\Controllers\Admin\CredentialController::class, 'store'])->name('credentials.store');
    Route::patch('credentials/{credential}', [\App\Http\Controllers\Admin\CredentialController::class, 'update'])->name('credentials.update');
    Route::post('credentials/{credential}/verificar', [\App\Http\Controllers\Admin\CredentialController::class, 'markVerified'])->name('credentials.mark-verified');
    Route::get('credentials/{credential}/revelar', [\App\Http\Controllers\Admin\CredentialController::class, 'reveal'])->name('credentials.reveal');
    Route::delete('credentials/{credential}', [\App\Http\Controllers\Admin\CredentialController::class, 'destroy'])->name('credentials.destroy');

    Route::post('time-entries', [\App\Http\Controllers\Admin\TimeEntryController::class, 'store'])->name('time-entries.store');
    Route::delete('time-entries/{timeEntry}', [\App\Http\Controllers\Admin\TimeEntryController::class, 'destroy'])->name('time-entries.destroy');

    /*
    |--------------------------------------------------------------------------
    | Licenses
    |--------------------------------------------------------------------------
    */

    Route::resource('licenses', LicenseController::class);
    Route::post('licenses/{license}/reset-demo', [LicenseController::class, 'resetDemo'])->name('licenses.reset-demo');

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    */

    Route::resource('plans', PlanController::class);

    /*
    |--------------------------------------------------------------------------
    | Planes de Hosting (catálogo)
    |--------------------------------------------------------------------------
    */

    Route::resource('hosting-plans', \App\Http\Controllers\Admin\HostingPlanController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Centro de Ayuda
    |--------------------------------------------------------------------------
    */

    Route::name('centro-de-ayuda.')->prefix('centro-de-ayuda')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\HelpCategoryController::class, 'index'])->name('index');

        Route::post('categorias', [\App\Http\Controllers\Admin\HelpCategoryController::class, 'store'])->name('categorias.store');
        Route::patch('categorias/{category}', [\App\Http\Controllers\Admin\HelpCategoryController::class, 'update'])->name('categorias.update');
        Route::delete('categorias/{category}', [\App\Http\Controllers\Admin\HelpCategoryController::class, 'destroy'])->name('categorias.destroy');

        Route::get('articulos/create', [\App\Http\Controllers\Admin\HelpArticleController::class, 'create'])->name('articulos.create');
        Route::post('articulos', [\App\Http\Controllers\Admin\HelpArticleController::class, 'store'])->name('articulos.store');
        Route::get('articulos/{article}/edit', [\App\Http\Controllers\Admin\HelpArticleController::class, 'edit'])->name('articulos.edit');
        Route::patch('articulos/{article}', [\App\Http\Controllers\Admin\HelpArticleController::class, 'update'])->name('articulos.update');
        Route::delete('articulos/{article}', [\App\Http\Controllers\Admin\HelpArticleController::class, 'destroy'])->name('articulos.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Finanzas — Facturación
    |--------------------------------------------------------------------------
    */

    Route::name('finanzas.facturacion.')->prefix('finanzas/facturacion')->group(function () {
        Route::get('/',              [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('index');
        Route::get('/nueva',         [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('create');
        Route::post('/',             [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}',     [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/imprimir', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('print');
        Route::post('/{invoice}/emitir',  [\App\Http\Controllers\Admin\InvoiceController::class, 'markIssued'])->name('mark-issued');
        Route::post('/{invoice}/anular',  [\App\Http\Controllers\Admin\InvoiceController::class, 'void'])->name('void');
        Route::delete('/{invoice}',  [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{invoice}/descargar', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('download');
        Route::post('/{invoice}/enviar', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('send-email');
    });

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */

    Route::get('configuracion',             [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('configuracion.index');
    Route::post('configuracion/{group}',      [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('configuracion.update');
    Route::post('configuracion/{group}/test', [\App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('configuracion.test');

    /*
    |--------------------------------------------------------------------------
    | Legales
    |--------------------------------------------------------------------------
    */

    Route::name('legales.')->prefix('legales')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LegalController::class, 'index'])->name('index');

        Route::resource('contratos', \App\Http\Controllers\Admin\ContractController::class)
            ->parameters(['contratos' => 'contract'])
            ->only(['index', 'create', 'store', 'show']);
        Route::post('contratos/{contract}/firmantes',      [\App\Http\Controllers\Admin\ContractController::class, 'addSigner'])->name('contratos.signers.store');
        Route::post('contratos/{contract}/enviar-firma',   [\App\Http\Controllers\Admin\ContractController::class, 'sendForSignature'])->name('contratos.send');
        Route::post('contratos/{contract}/marcar-firmado', [\App\Http\Controllers\Admin\ContractController::class, 'markSignedManually'])->name('contratos.mark-signed');
        Route::post('contratos/{contract}/cancelar',       [\App\Http\Controllers\Admin\ContractController::class, 'cancel'])->name('contratos.cancel');
        Route::get('contratos/{contract}/imprimir',        [\App\Http\Controllers\Admin\ContractController::class, 'print'])->name('contratos.print');

        Route::resource('plantillas', \App\Http\Controllers\Admin\ContractTemplateController::class)
            ->parameters(['plantillas' => 'template'])
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('plantillas/{template}/versiones', [\App\Http\Controllers\Admin\ContractTemplateController::class, 'versions'])->name('plantillas.versions');
    });

    /*
    |--------------------------------------------------------------------------
    | Cotizaciones (CRM — Etapa 2)
    |--------------------------------------------------------------------------
    */

    Route::resource('cotizaciones', \App\Http\Controllers\Admin\QuoteController::class)
        ->parameters(['cotizaciones' => 'quote'])
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('cotizaciones/{quote}/enviar',           [\App\Http\Controllers\Admin\QuoteController::class, 'send'])->name('cotizaciones.send');
    Route::post('cotizaciones/{quote}/marcar-aceptada',  [\App\Http\Controllers\Admin\QuoteController::class, 'markAccepted'])->name('cotizaciones.mark-accepted');
    Route::post('cotizaciones/{quote}/rechazar',         [\App\Http\Controllers\Admin\QuoteController::class, 'reject'])->name('cotizaciones.reject');
    Route::get('cotizaciones/{quote}/imprimir',          [\App\Http\Controllers\Admin\QuoteController::class, 'print'])->name('cotizaciones.print');
    Route::get('cotizaciones/{quote}/descargar',         [\App\Http\Controllers\Admin\QuoteController::class, 'download'])->name('cotizaciones.download');

    /*
    |--------------------------------------------------------------------------
    | Notificaciones
    |--------------------------------------------------------------------------
    */

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/unread',     [\App\Http\Controllers\Admin\NotificationController::class, 'unread'])->name('unread');
        Route::post('/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('markRead');
    });

    /*
    |--------------------------------------------------------------------------
    | Tickets de Soporte
    |--------------------------------------------------------------------------
    */

    Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);

    // Acciones AJAX para tickets
    Route::patch('tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('tickets/{ticket}/assign', [\App\Http\Controllers\Admin\TicketController::class, 'assignTicket'])->name('tickets.assign');

    /*
    |--------------------------------------------------------------------------
    | App Versions
    |--------------------------------------------------------------------------
    */

    Route::get('app-versions',          [\App\Http\Controllers\Admin\AppVersionController::class, 'index'])->name('app-versions.index');
    Route::post('app-versions',         [\App\Http\Controllers\Admin\AppVersionController::class, 'store'])->name('app-versions.store');
    Route::post('app-versions/push',    [\App\Http\Controllers\Admin\AppVersionController::class, 'pushUpdates'])->name('app-versions.push');

    /*
    |--------------------------------------------------------------------------
    | Demos
    |--------------------------------------------------------------------------
    */

    Route::get('demos',                [\App\Http\Controllers\Admin\DemoController::class, 'index'])        ->name('demos.index');
    Route::post('demos/provision',     [\App\Http\Controllers\Admin\DemoController::class, 'provision'])    ->name('demos.provision');
    Route::post('demos/reset',         [\App\Http\Controllers\Admin\DemoController::class, 'reset'])        ->name('demos.reset');
    Route::post('demos/factory-reset', [\App\Http\Controllers\Admin\DemoController::class, 'factoryReset'])->name('demos.factory-reset');

});

require __DIR__.'/auth.php';