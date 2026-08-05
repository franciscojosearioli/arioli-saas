<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $pdfBytes,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->invoice->number ? "Factura {$this->invoice->number}" : 'Factura';

        return new Envelope(
            subject: $subject,
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-generated',
            with: ['invoice' => $this->invoice],
        );
    }

    public function attachments(): array
    {
        $filename = $this->invoice->number ?: 'factura';

        return [
            Attachment::fromData(fn () => $this->pdfBytes, "{$filename}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
