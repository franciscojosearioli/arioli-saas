<?php

namespace App\Services\Contracts;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;

/**
 * Único punto del sistema que genera el PDF de un contrato — para verlo
 * en el navegador (vista imprimible) o para adjuntarlo a un email. El
 * motor de PDF (dompdf) vive únicamente acá — controller, rutas y
 * mailables no lo conocen.
 */
class ContractPdfService
{
    public function render(Contract $contract): View
    {
        $contract->loadMissing(['signers.signature', 'template']);

        return view('admin.legales.contratos.print', compact('contract'));
    }

    /**
     * @return string bytes del PDF, listos para adjuntar a un email o descargar.
     */
    public function renderPdf(Contract $contract): string
    {
        $contract->loadMissing(['signers.signature', 'template']);

        return Pdf::loadView('admin.legales.contratos.pdf', compact('contract'))
            ->setPaper('a4')
            ->output();
    }
}
