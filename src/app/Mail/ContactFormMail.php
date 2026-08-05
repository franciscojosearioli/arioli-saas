<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $type,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $company,
        public ?string $productName,
        public ?string $inquiryType,
        public string $body,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'contacto' => 'Nueva consulta desde arioli.dev',
            'partner'  => 'Nueva consulta de partner — arioli.dev',
            'producto' => "Consulta sobre {$this->productName} — arioli.dev",
            'servicio' => "Consulta de servicio: {$this->productName} — arioli.dev",
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Nueva consulta — arioli.dev',
            replyTo: [$this->email => $this->name],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form',
            with: [
                'type'        => $this->type,
                'name'        => $this->name,
                'email'       => $this->email,
                'phone'       => $this->phone,
                'company'     => $this->company,
                'productName' => $this->productName,
                'inquiryType' => $this->inquiryType,
                'body'        => $this->body,
            ],
        );
    }
}
