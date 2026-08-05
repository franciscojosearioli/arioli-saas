<?php

namespace App\Mail;

use App\Contracts\AssetInterface;
use App\Models\ClientContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssetExpirationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AssetInterface $asset,
        public ClientContact $contact,
        public int $daysRemaining,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ {$this->asset->label()} vence en {$this->daysRemaining} días — Arioli.dev",
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.asset-expiration',
            with: [
                'assetLabel'    => $this->asset->label(),
                'contact'       => $this->contact,
                'daysRemaining' => $this->daysRemaining,
                'expiresAt'     => $this->asset->expiresAt()?->format('d/m/Y'),
                'renewalPayer'  => $this->asset->renewalPayer(),
            ],
        );
    }
}
