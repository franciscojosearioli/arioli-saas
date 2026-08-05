<?php

namespace App\Mail;

use App\Models\License;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseExpirationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public License $license,
        public User $user,
        public int $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ Tu licencia vence en {$this->daysRemaining} días — Arioli.dev",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.license-expiration',
            with: [
                'license'       => $this->license,
                'user'          => $this->user,
                'daysRemaining' => $this->daysRemaining,
                'panelUrl' => 'https://' . config('app.cliente_domain') . '/login',
                'productName'   => $this->license->plan->product->name ?? 'tu sistema',
                'expiresAt'     => $this->license->expires_at->format('d/m/Y'),
            ],
        );
    }
}