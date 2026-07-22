<?php

namespace App\Mail;

use App\Models\Consentimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsentimientoFirmaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Consentimiento $consentimiento)
    {
    }

    public function envelope(): Envelope
    {
        $tipo     = $this->consentimiento->tipo?->nombre ?? 'Consentimiento Informado';
        $paciente = $this->consentimiento->paciente;
        $nombre   = $paciente ? $paciente->apellido . ', ' . $paciente->nombre : '';

        return new Envelope(
            subject: "Firma requerida: {$tipo} — {$nombre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consentimiento-firma',
        );
    }
}
