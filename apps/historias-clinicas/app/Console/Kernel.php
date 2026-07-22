<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('agenda:recordatorios')->dailyAt('08:00');

        // Etapa 6.3.2 (ver docs/ARQUITECTURA_MODULAR.md): ciclo de vida
        // automático de DemoInstance, orquestando los comandos ya
        // validados a mano en 6.3.1 — nunca reimplementa su lógica.
        $schedule->command('demo:expirar-vencidas')->hourly();
        $schedule->command('demo:limpiar-vencidas')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}