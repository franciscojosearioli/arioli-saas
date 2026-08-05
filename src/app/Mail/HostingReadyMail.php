<?php

namespace App\Mail;

use App\Models\ClientContact;
use App\Models\HostingAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirmación de pago + hosting listo. Nunca lleva la contraseña generada —
 * lleva un link firmado (temporarySignedRoute) a una pantalla donde el
 * cliente define su propia contraseña, mismo principio de confianza que ya
 * usa SignatureController para firma electrónica.
 */
class HostingReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HostingAccount $account,
        public ClientContact $contact,
        public string $credentialsUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Tu hosting ya está listo — Arioli.dev',
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hosting-ready',
            with: [
                'contact'        => $this->contact,
                'username'       => $this->account->remote_username,
                'credentialsUrl' => $this->credentialsUrl,
            ],
        );
    }
}
