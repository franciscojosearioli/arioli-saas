<?php

namespace App\Notifications;

use App\Models\Agenda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgendaRecordatorio extends Notification
{
    use Queueable;

    public function __construct(public Agenda $agenda) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $agenda    = $this->agenda;
        $paciente  = $agenda->paciente;
        $inicio    = $agenda->fecha_hora_inicio->format('d/m/Y H:i');
        $fin       = $agenda->fecha_hora_fin->format('H:i');
        $modalidad = $agenda->modalidad === 'virtual' ? 'Virtual' : 'Presencial';

        $mail = (new MailMessage)
            ->subject("Recordatorio de cita — {$inicio}")
            ->greeting("Hola {$agenda->getNombreProfesional()},")
            ->line("Le recordamos que tiene una cita programada para mañana:")
            ->line("**Paciente:** {$paciente->nombre} {$paciente->apellido}")
            ->line("**Fecha y hora:** {$inicio} — {$fin} hs")
            ->line("**Modalidad:** {$modalidad}")
            ->line("**Motivo:** {$agenda->motivo}");

        if ($agenda->modalidad === 'virtual' && $agenda->link_virtual) {
            $mail->action('Unirse a la reunión', $agenda->link_virtual);
        } else {
            $mail->action('Ver detalle de la cita', route('panel.agenda.show', $agenda->id));
        }

        if ($agenda->notas) {
            $mail->line("**Notas adicionales:** {$agenda->notas}");
        }

        return $mail->line('Si necesita reprogramar, comuníquese con el sistema.')
            ->salutation(\App\Models\ConfiguracionSistema::instancia()->nombre_sistema);
    }
}
