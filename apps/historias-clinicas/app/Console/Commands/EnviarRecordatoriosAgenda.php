<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Notifications\AgendaRecordatorio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EnviarRecordatoriosAgenda extends Command
{
    protected $signature   = 'agenda:recordatorios';
    protected $description = 'Envía recordatorios de citas programadas para las próximas 24 horas';

    public function handle(): int
    {
        $desde = Carbon::now();
        $hasta = Carbon::now()->addHours(24);

        $agendas = Agenda::with(['paciente', 'profesional'])
            ->whereBetween('fecha_hora_inicio', [$desde, $hasta])
            ->where('estado', '!=', 'cancelado')
            ->where('recordatorio_enviado', false)
            ->get();

        if ($agendas->isEmpty()) {
            $this->info('No hay citas próximas para recordar.');
            return self::SUCCESS;
        }

        $enviados = 0;

        foreach ($agendas as $agenda) {
            $email = $agenda->getEmailNotificacion();

            if (! $email) {
                $this->warn("Cita #{$agenda->id}: sin email de notificación, se omite.");
                continue;
            }

            try {
                Notification::route('mail', $email)
                    ->notify(new AgendaRecordatorio($agenda));

                $agenda->update(['recordatorio_enviado' => true]);
                $enviados++;

                $this->info("Recordatorio enviado a {$email} para cita #{$agenda->id} ({$agenda->fecha_hora_inicio->format('d/m/Y H:i')})");
            } catch (\Exception $e) {
                $this->error("Error al enviar a {$email}: {$e->getMessage()}");
            }
        }

        $this->info("Proceso finalizado. Enviados: {$enviados} / {$agendas->count()}.");
        return self::SUCCESS;
    }
}
