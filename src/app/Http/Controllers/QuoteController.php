<?php

namespace App\Http\Controllers;

use App\Enums\QuoteStatus;
use App\Services\Quotes\QuoteAcceptanceService;
use App\Services\Quotes\QuotePdfService;
use App\Models\Quote;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    public function show(string $token): View
    {
        $quote = Quote::where('public_token', $token)->first();

        if (! $quote) {
            return view('public.cotizacion-error', ['reason' => 'not_found']);
        }

        if ($quote->status === QuoteStatus::Aceptada) {
            return view('public.cotizacion-error', ['reason' => 'already_accepted']);
        }

        if ($quote->status === QuoteStatus::Rechazada) {
            return view('public.cotizacion-error', ['reason' => 'already_rejected']);
        }

        if (! $quote->isTokenValid()) {
            return view('public.cotizacion-error', ['reason' => 'expired']);
        }

        $quote->load('items');

        return view('public.cotizacion', ['quote' => $quote]);
    }

    public function download(string $token, QuotePdfService $pdf): View|Response
    {
        $quote = Quote::where('public_token', $token)->first();

        if (! $quote) {
            return view('public.cotizacion-error', ['reason' => 'not_found']);
        }

        if (! $quote->isTokenValid() && $quote->status !== QuoteStatus::Aceptada) {
            return view('public.cotizacion-error', ['reason' => 'expired']);
        }

        return $pdf->download($quote);
    }

    public function accept(string $token, QuoteAcceptanceService $acceptance): View
    {
        $quote = Quote::where('public_token', $token)->first();

        if (! $quote || ! $quote->isTokenValid() || $quote->status !== QuoteStatus::Enviada) {
            return view('public.cotizacion-error', ['reason' => 'expired']);
        }

        $acceptance->accept($quote);

        return view('public.cotizacion-exito', ['quote' => $quote]);
    }

    public function reject(string $token, QuoteAcceptanceService $acceptance): View
    {
        $quote = Quote::where('public_token', $token)->first();

        if (! $quote || ! $quote->isTokenValid() || $quote->status !== QuoteStatus::Enviada) {
            return view('public.cotizacion-error', ['reason' => 'expired']);
        }

        $acceptance->reject($quote);

        return view('public.cotizacion-exito', ['quote' => $quote, 'rejected' => true]);
    }
}
