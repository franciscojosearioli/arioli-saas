<?php

use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\ValidateLicense;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.key' => ValidateApiKey::class,
            'license' => ValidateLicense::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Recomendado por Horizon: recolecta métricas para el dashboard.
        // Las tareas de negocio (vencimientos, recordatorios, etc.) se
        // suman acá recién cuando exista el módulo que las necesita.
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // §09: el worker de sincronización de Publicaciones — polling de
        // outbox_events, no una cola dedicada todavía (ver el comando).
        // outbox_events es una tabla por-tenant: hay que correr el comando
        // dentro del contexto de cada tenant (tenants:run de Stancl), no
        // contra la conexión default (la base landlord no tiene esa tabla).
        $schedule->command('tenants:run', ['publicaciones:sincronizar'])->everyMinute()->withoutOverlapping();

        // §09 Fase 4: chequeo diario de vencimiento de CuentaConectada —
        // igual que arriba, por-tenant vía tenants:run (cuentas_conectadas
        // es una tabla por-tenant).
        $schedule->command('tenants:run', ['cuentas-conectadas:revisar-vencimientos'])->daily()->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
