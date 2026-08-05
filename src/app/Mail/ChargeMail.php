<?php

namespace App\Mail;

use App\Models\Charge;
use App\Models\ClientContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChargeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Charge $charge,
        public ClientContact $contact,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Cobro: {$this->charge->concept} — Arioli.dev",
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.charge-generated',
            with: [
                'charge'  => $this->charge,
                'contact' => $this->contact,
            ],
        );
    }
}
