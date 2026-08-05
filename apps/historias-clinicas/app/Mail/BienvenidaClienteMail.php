<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Etapa 6.5: nunca lleva una contraseña — solo el link firmado para que
 * el cliente defina la suya. Enviado directo desde historias-clinicas
 * (no depende del mail centralizado, que hoy envía la contraseña en
 * texto plano — ver Gate G-02 en docs/ARQUITECTURA_MODULAR.md).
 */
class BienvenidaClienteMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $adminNombre,
        public string $urlReclamoCredenciales,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a Sistema de Salud — activá tu cuenta',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenida-cliente',
        );
    }
}
