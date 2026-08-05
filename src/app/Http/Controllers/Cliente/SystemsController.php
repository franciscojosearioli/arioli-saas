<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SystemsController extends Controller
{
    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with(['licenses.plan.product', 'licenses.domain'])->firstOrFail();

        return view('cliente.systems.index', [
            'licenses' => $client->licenses,
        ]);
    }
}
