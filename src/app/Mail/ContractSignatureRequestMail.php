<?php

namespace App\Mail;

use App\Models\ContractSigner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignatureRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContractSigner $signer,
        public string $signingUrl,
        public string $pdfBytes,
        public bool $isReminder = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isReminder
                ? "Recordatorio: falta tu firma — {$this->signer->contract->title}"
                : "Tenés un documento para firmar — {$this->signer->contract->title}",
            replyTo: [config('services.contact.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-signature-request',
            with: [
                'signer'     => $this->signer,
                'contract'   => $this->signer->contract,
                'signingUrl' => $this->signingUrl,
                'isReminder' => $this->isReminder,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, "{$this->signer->contract->title}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
