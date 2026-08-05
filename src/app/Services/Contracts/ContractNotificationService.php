<?php

namespace App\Services\Contracts;

use App\Mail\ContractRejectedMail;
use App\Mail\ContractSignatureRequestMail;
use App\Mail\ContractSignedMail;
use App\Models\Contract;
use App\Models\ContractSigner;
use Illuminate\Support\Facades\Mail;

/**
 * Único punto del sistema que envía emails relacionados a contratos.
 * Ni los controllers ni los providers de firma llaman Mail:: directamente
 * — así, pasar a colas (Mail::queue / ShouldQueue en los Mailables) es un
 * cambio interno acá, no en el resto del módulo.
 */
class ContractNotificationService
{
    public function __construct(private readonly ContractPdfService $pdf) {}

    public function sendSignatureRequest(ContractSigner $signer, string $signingUrl): void
    {
        $pdfBytes = $this->pdf->renderPdf($signer->contract);

        Mail::to($signer->email)->send(new ContractSignatureRequestMail($signer, $signingUrl, $pdfBytes));
    }

    public function sendReminder(ContractSigner $signer, string $signingUrl): void
    {
        $pdfBytes = $this->pdf->renderPdf($signer->contract);

        Mail::to($signer->email)->send(new ContractSignatureRequestMail($signer, $signingUrl, $pdfBytes, isReminder: true));
    }

    public function notifySigned(Contract $contract): void
    {
        $pdfBytes = $this->pdf->renderPdf($contract);

        foreach ($contract->signers as $signer) {
            Mail::to($signer->email)->send(new ContractSignedMail($contract, $pdfBytes));
        }
    }

    public function notifyRejected(Contract $contract, ContractSigner $signer): void
    {
        $address = config('services.contact.address');

        if ($address) {
            Mail::to($address)->send(new ContractRejectedMail($contract, $signer));
        }
    }
}
