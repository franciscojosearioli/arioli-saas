<?php

namespace App\Mail;

use App\Models\ClientContact;
use App\Models\ClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceConfirmationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientService $service,
        public ClientContact $contact,
        public string $confirmUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¿Hacemos el mantenimiento de este mes? — Arioli.dev',
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.maintenance-confirmation-request',
            with: [
                'service'    => $this->service,
                'contact'    => $this->contact,
                'confirmUrl' => $this->confirmUrl,
            ],
        );
    }
}
