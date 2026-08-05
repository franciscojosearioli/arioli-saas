<?php

namespace App\Mail;

use App\Models\Contract;
use App\Models\ContractSigner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract, public ContractSigner $signer) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Un firmante rechazó un contrato — {$this->contract->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-rejected',
            with: ['contract' => $this->contract, 'signer' => $this->signer],
        );
    }
}
