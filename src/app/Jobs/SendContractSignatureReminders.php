<?php

namespace App\Jobs;

use App\Enums\ContractEventType;
use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Support\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Aviso interno (nunca mail al cliente) cuando un contrato lleva demasiado
 * tiempo esperando la firma — el reenvío al cliente sigue siendo una acción
 * manual del admin desde Legales (ContractController::sendForSignature).
 */
class SendContractSignatureReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $daysPendingToNotify = [7, 15, 30];

        foreach ($daysPendingToNotify as $daysPending) {
            $contracts = Contract::where('status', ContractStatus::PendingSignature)
                ->where('requires_signature', true)
                ->whereHas('events', function ($q) use ($daysPending) {
                    $q->where('event', ContractEventType::Sent)
                        ->whereDate('created_at', now()->subDays($daysPending)->toDateString());
                })
                ->get();

            foreach ($contracts as $contract) {
                try {
                    NotificationHelper::contractPendingSignature($contract, $daysPending);

                    Log::info('Contract signature reminder logged', [
                        'contract_id' => $contract->id,
                        'days'        => $daysPending,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Contract signature reminder error', [
                        'contract_id' => $contract->id,
                        'message'     => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
