<?php

namespace App\Http\Controllers\Cliente;

use App\Enums\ClientServiceStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServicesController extends Controller
{
    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with('services')->firstOrFail();

        return view('cliente.services.index', [
            'services' => $client->services->where('status', ClientServiceStatus::Active)->values(),
        ]);
    }
}
