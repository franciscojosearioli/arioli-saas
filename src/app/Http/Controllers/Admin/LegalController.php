<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContractStatus;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-legal');

        $pending = Contract::with('signers')
            ->where('status', ContractStatus::PendingSignature)
            ->latest()
            ->limit(8)
            ->get();

        $recentlySigned = Contract::where('status', ContractStatus::Signed)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $stats = [
            'draft'   => Contract::where('status', ContractStatus::Draft)->count(),
            'pending' => Contract::where('status', ContractStatus::PendingSignature)->count(),
            'signed'  => Contract::where('status', ContractStatus::Signed)->count(),
            'rejected' => Contract::where('status', ContractStatus::Rejected)->count(),
        ];

        return view('admin.legales.index', compact('pending', 'recentlySigned', 'stats'));
    }
}
