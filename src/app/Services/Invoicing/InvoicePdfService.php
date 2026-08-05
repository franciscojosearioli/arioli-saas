<?php

namespace App\Services\Invoicing;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Único punto del sistema que genera el PDF de una factura — mismo criterio
 * que QuotePdfService/ContractPdfService: el motor de PDF vive únicamente acá.
 */
class InvoicePdfService
{
    public function renderPdf(Invoice $invoice): string
    {
        $invoice->loadMissing(['order.plan.product', 'charge']);

        return Pdf::loadView('admin.invoices.pdf', compact('invoice'))
            ->setPaper('a4')
            ->output();
    }

    public function download(Invoice $invoice): Response
    {
        $invoice->loadMissing(['order.plan.product', 'charge']);

        $filename = ($invoice->number ?: "factura-borrador-{$invoice->id}") . '.pdf';

        return Pdf::loadView('admin.invoices.pdf', compact('invoice'))
            ->setPaper('a4')
            ->download($filename);
    }
}
