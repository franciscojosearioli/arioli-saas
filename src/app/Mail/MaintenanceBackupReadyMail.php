<?php

namespace App\Mail;

use App\Models\ClientContact;
use App\Models\ClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceBackupReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientService $service,
        public ClientContact $contact,
        public string $downloadUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu backup de mantenimiento ya está listo — Arioli.dev',
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.maintenance-backup-ready',
            with: [
                'service'     => $this->service,
                'contact'     => $this->contact,
                'downloadUrl' => $this->downloadUrl,
            ],
        );
    }
}
