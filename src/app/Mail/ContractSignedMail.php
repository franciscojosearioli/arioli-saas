<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract, public string $pdfBytes) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Contrato firmado por todas las partes — {$this->contract->title}",
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-signed',
            with: ['contract' => $this->contract],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, "{$this->contract->title} - firmado.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
