<?php

namespace App\Services\Quotes;

use App\Enums\ClientEventType;
use App\Enums\QuoteStatus;
use App\Mail\QuoteMail;
use App\Models\ClientEvent;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Único punto del sistema que envía emails de cotizaciones — mismo criterio
 * que ContractNotificationService: ni el controller ni el servicio de
 * aceptación llaman Mail:: directamente.
 */
class QuoteNotificationService
{
    public function __construct(private readonly QuotePdfService $pdf) {}

    public function sendForApproval(Quote $quote): string
    {
        $quote->update([
            'public_token'            => Str::random(64),
            'public_token_expires_at' => now()->addDays(14),
            'status'                  => QuoteStatus::Enviada,
            'sent_at'                 => now(),
        ]);

        $contact = $quote->client->contacts()->where('is_primary', true)->first()
            ?? $quote->client->contacts()->first();

        $publicUrl = route('quotes.public.show', $quote->public_token);

        if ($contact?->email) {
            $pdfBytes = $this->pdf->renderPdf($quote);
            Mail::to($contact->email)->send(new QuoteMail($quote, $publicUrl, $pdfBytes));
        }

        ClientEvent::log(
            $quote->client,
            "Se envió la propuesta \"{$quote->title}\"",
            ClientEventType::Quote,
            $quote,
        );

        return $publicUrl;
    }
}
