<?php

namespace App\Http\Controllers;

use App\Enums\ContractEventType;
use App\Enums\SignerStatus;
use App\Models\ContractEvent;
use App\Models\ContractSignature;
use App\Models\ContractSigner;
use App\Services\Contracts\ContractNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SignatureController extends Controller
{
    public function show(string $token): View
    {
        $signer = ContractSigner::where('signing_token', $token)->first();

        if (! $signer) {
            return view('public.firmar-error', ['reason' => 'not_found']);
        }

        if ($signer->status === SignerStatus::Signed) {
            return view('public.firmar-error', ['reason' => 'already_signed']);
        }

        if ($signer->status === SignerStatus::Rejected) {
            return view('public.firmar-error', ['reason' => 'already_rejected']);
        }

        if (! $signer->isTokenValid()) {
            return view('public.firmar-error', ['reason' => 'expired']);
        }

        $alreadyOpened = ContractEvent::where('contract_signer_id', $signer->id)
            ->where('event', ContractEventType::Opened)
            ->exists();

        if (! $alreadyOpened) {
            ContractEvent::log($signer->contract, ContractEventType::Opened, $signer, [
                'ip' => request()->ip(),
            ]);
        }

        $signer->load('contract');

        return view('public.firmar', ['signer' => $signer, 'contract' => $signer->contract]);
    }

    public function sign(Request $request, string $token, ContractNotificationService $notifier): RedirectResponse|View
    {
        $signer = ContractSigner::where('signing_token', $token)->first();

        if (! $signer || ! $signer->isTokenValid()) {
            return view('public.firmar-error', ['reason' => 'expired']);
        }

        $request->validate([
            'confirm_name' => 'required|string|max:255',
            'accept'       => 'required|accepted',
        ]);

        $contract = $signer->contract;

        ContractSignature::create([
            'contract_signer_id' => $signer->id,
            'ip_address'          => $request->ip(),
            'user_agent'          => (string) $request->userAgent(),
            'content_hash'        => hash('sha256', $contract->content),
        ]);

        $signer->update([
            'status'        => SignerStatus::Signed,
            'signed_at'     => now(),
            'signing_token' => null,
        ]);

        ContractEvent::log($contract, ContractEventType::Signed, $signer, [
            'confirm_name' => $request->input('confirm_name'),
        ]);

        $contract->recalculateStatus();

        if ($contract->fresh()->status->value === 'signed') {
            $notifier->notifySigned($contract);
        }

        return view('public.firmar-exito', ['signer' => $signer]);
    }

    public function reject(Request $request, string $token, ContractNotificationService $notifier): View
    {
        $signer = ContractSigner::where('signing_token', $token)->first();

        if (! $signer || ! $signer->isTokenValid()) {
            return view('public.firmar-error', ['reason' => 'expired']);
        }

        $contract = $signer->contract;

        $signer->update(['status' => SignerStatus::Rejected, 'signing_token' => null]);

        ContractEvent::log($contract, ContractEventType::Rejected, $signer);

        $contract->recalculateStatus();

        $notifier->notifyRejected($contract, $signer);

        return view('public.firmar-exito', ['signer' => $signer, 'rejected' => true]);
    }
}
