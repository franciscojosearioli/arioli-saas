<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Services\Clients\ClientServiceHealthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceStatusController extends Controller
{
    public function index(ClientServiceHealthService $health): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with(['hostings', 'domains', 'sslCertificates', 'cloudflareServices'])->firstOrFail();

        return view('cliente.service-status.index', $health->calculate($client));
    }
}
