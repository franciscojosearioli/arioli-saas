<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HistoriasClinicasWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $systemUrl;

    public function __construct(
        public readonly string $clientName,
        public readonly string $clientEmail,
        public readonly string $tenantId,
        public readonly string $adminPassword,
        public readonly string $publicDomain,
    ) {
        $this->systemUrl = 'https://' . $tenantId . '.' . $publicDomain . '.' . config('app.tenant_domain');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu sistema Clínica está listo!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.historias-clinicas-welcome',
        );
    }
}
