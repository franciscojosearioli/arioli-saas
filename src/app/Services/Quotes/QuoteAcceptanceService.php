<?php

namespace App\Services\Quotes;

use App\Enums\ChargeStatus;
use App\Enums\ClientEventType;
use App\Enums\ClientServiceStatus;
use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\Currency;
use App\Enums\JobStatus;
use App\Enums\QuoteItemType;
use App\Enums\QuoteStatus;
use App\Enums\SignerRole;
use App\Enums\SignerStatus;
use App\Models\Charge;
use App\Models\ClientEvent;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Quote;

/**
 * Genera Proyecto + Servicios/Trabajos + Contrato (a firmar) + Cobro inicial
 * a partir de una Quote aceptada — reutiliza el flujo de Legales existente
 * para pedir la firma real (no la auto-firma, a diferencia de las licencias):
 * el Contract se crea en Draft con un firmante Pending, y el admin lo envía
 * a firma con el flujo de ContractController ya existente, sin cambios ahí.
 */
class QuoteAcceptanceService
{
    public function accept(Quote $quote): Contract
    {
        $client = $quote->client;
        $quote->loadMissing('items');

        $project = null;

        if ($quote->creates_project) {
            $project = $client->projects()->create([
                'name'   => $quote->project_name ?? $quote->title,
                'type'   => $quote->project_type ?? 'otro',
                'status' => 'active',
            ]);
        }

        foreach ($quote->items as $item) {
            $amount = $item->subtotal;

            if ($item->item_type === QuoteItemType::Service) {
                $client->services()->create([
                    'project_id'    => $project?->id,
                    'service_type'  => $item->service_type,
                    'billing_cycle' => $item->billing_cycle,
                    'amount'        => $amount,
                    'starts_at'     => now(),
                    'status'        => ClientServiceStatus::Active,
                ]);
            } else {
                $client->jobs()->create([
                    'project_id'   => $project?->id,
                    'title'        => $item->description,
                    'amount'       => $amount,
                    'status'       => JobStatus::Aprobado,
                    'requested_at' => now(),
                ]);
            }
        }

        $contact = $client->contacts()->where('is_primary', true)->first()
            ?? $client->contacts()->first();

        $contract = Contract::create([
            'tenant_id'            => $client->licenses()->value('tenant_id'),
            'client_id'            => $client->id,
            'contractable_type'    => $project ? Project::class : null,
            'contractable_id'      => $project?->id,
            'title'                => "Contrato de servicio — {$quote->title}",
            'type'                 => ContractType::Servicio,
            'content'              => $this->defaultContractContent($quote),
            'status'               => ContractStatus::Draft,
            'requires_signature'   => true,
        ]);

        $contract->signers()->create([
            'role'   => SignerRole::Cliente,
            'name'   => $contact?->name ?? $client->name,
            'email'  => $contact?->email ?? '',
            'status' => SignerStatus::Pending,
        ]);

        $charge = null;

        if ($quote->initial_charge_amount) {
            $charge = Charge::create([
                'client_id' => $client->id,
                'concept'   => "Cobro inicial — {$quote->title}",
                'amount'    => $quote->initial_charge_amount,
                'currency'  => Currency::ARS,
                'status'    => ChargeStatus::Pending,
            ]);
        }

        $quote->update([
            'status'      => QuoteStatus::Aceptada,
            'decided_at'  => now(),
            'project_id'  => $project?->id,
            'contract_id' => $contract->id,
        ]);

        ClientEvent::log(
            $client,
            "Se aceptó la propuesta \"{$quote->title}\"",
            ClientEventType::Quote,
            $quote,
            ['project_id' => $project?->id, 'contract_id' => $contract->id, 'charge_id' => $charge?->id],
        );

        return $contract;
    }

    public function reject(Quote $quote): void
    {
        $quote->update(['status' => QuoteStatus::Rechazada, 'decided_at' => now()]);

        ClientEvent::log(
            $quote->client,
            "Se rechazó la propuesta \"{$quote->title}\"",
            ClientEventType::Quote,
            $quote,
        );
    }

    private function defaultContractContent(Quote $quote): string
    {
        $lines = $quote->items->map(
            fn ($item) => "- {$item->description}: {$item->quantity} x {$item->currency->value} " . number_format($item->unit_price, 2) . " = {$item->currency->value} " . number_format($item->subtotal, 2)
        )->implode("\n");

        return <<<TEXT
        CONTRATO DE SERVICIO

        En base a la propuesta aceptada "{$quote->title}", se detallan los ítems contratados:

        {$lines}

        Total: {$this->formattedTotal($quote)}

        Este documento requiere la firma del cliente para quedar vigente.
        TEXT;
    }

    private function formattedTotal(Quote $quote): string
    {
        return collect($quote->totalsByCurrency())
            ->map(fn ($amount, $currency) => "{$currency} " . number_format($amount, 2))
            ->implode(' + ');
    }
}
