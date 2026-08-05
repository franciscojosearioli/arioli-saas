<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {

            // ADMIN PANEL
            Route::middleware('web')
                ->domain(config('app.admin_domain'))
                ->group(base_path('routes/web.php'));

            // LANDING PÚBLICA
            Route::middleware('web')
                ->domain(config('app.landing_domain'))
                ->group(base_path('routes/landing.php'));

            // PANEL DEL CLIENTE
            Route::middleware('web')
                ->domain(config('app.cliente_domain'))
                ->group(base_path('routes/cliente.php'));

            // API DE LICENCIAS (dominio admin, sin sesión ni CSRF)
            Route::middleware('api')
                ->domain(config('app.admin_domain'))
                ->prefix('api')
                ->group(base_path('routes/api.php'));

        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enterprise: Cache middleware aliases
        $middleware->alias([
            'verify.license'  => \App\Http\Middleware\VerifyLicenseMiddleware::class,
            'auth.cliente'    => \App\Http\Middleware\RedirectIfNotClienteAuthenticated::class,
            'api.key'         => \App\Http\Middleware\ValidateApiKey::class,
            'audit.trail'     => \App\Http\Middleware\AuditTrailMiddleware::class,
            'tenant.session'  => \App\Http\Middleware\TenantSessionMiddleware::class,
            'tenant.cache'    => \App\Http\Middleware\TenantCacheMiddleware::class,
        ]);

        // Enterprise: Automatic cache optimization for web routes (temporarily disabled)
        // $middleware->web([
        //     \App\Http\Middleware\TenantCacheMiddleware::class,
        // ]);

        // Enterprise: Performance optimization for API routes (temporarily disabled)
        // $middleware->api([
        //     \App\Http\Middleware\TenantCacheMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel convierte ModelNotFoundException en NotFoundHttpException antes de
        // llegar a los callbacks de render() — hay que inspeccionar la excepción original
        // (getPrevious()) para poder distinguir de qué modelo se trata.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                && $previous->getModel() === \App\Models\Contract::class) {
                return response()->view('errors.contract-not-found', [], 404);
            }
        });
    })->create();