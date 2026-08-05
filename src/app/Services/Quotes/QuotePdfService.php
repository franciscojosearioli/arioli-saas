<?php

namespace App\Services\Quotes;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Único punto del sistema que genera el PDF de una cotización — mismo
 * criterio que ContractPdfService: el motor de PDF vive únicamente acá.
 */
class QuotePdfService
{
    public function render(Quote $quote): View
    {
        $quote->loadMissing(['items', 'client']);

        return view('admin.cotizaciones.pdf', compact('quote'));
    }

    public function renderPdf(Quote $quote): string
    {
        $quote->loadMissing(['items', 'client']);

        return Pdf::loadView('admin.cotizaciones.pdf', compact('quote'))
            ->setPaper('a4')
            ->output();
    }

    public function download(Quote $quote): Response
    {
        $quote->loadMissing(['items', 'client']);

        $filename = str($quote->title)->slug() . '.pdf';

        return Pdf::loadView('admin.cotizaciones.pdf', compact('quote'))
            ->setPaper('a4')
            ->download($filename);
    }
}
