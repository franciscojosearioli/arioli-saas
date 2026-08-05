<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
        public string $publicUrl,
        public string $pdfBytes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Propuesta: {$this->quote->title}",
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-sent',
            with: [
                'quote'     => $this->quote,
                'publicUrl' => $this->publicUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, "{$this->quote->title}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
