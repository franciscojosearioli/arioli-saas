<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Mail\HostingReadyMail;
use App\Models\ClientEvent;
use App\Models\Hosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class HostingController extends Controller
{
    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with([
            'hostings.hostingPlan', 'hostings.account', 'hostings.projects.domain', 'hostings.projects.license.plan.product',
        ])->firstOrFail();

        return view('cliente.hosting.index', [
            'hostings' => $client->hostings,
        ]);
    }

    public function resendAccess(Hosting $hosting): RedirectResponse
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $hosting->client_id !== $user->client_id, 403);

        $account = $hosting->account;

        if (! $account) {
            return back()->with('error', 'Este hosting todavía no tiene una cuenta técnica creada. Escribinos si necesitás ayuda.');
        }

        if ($account->credential_claimed_at) {
            return back()->with('error', 'Ya definiste tu contraseña de acceso — si la olvidaste, contactanos para restablecerla.');
        }

        $contact = $hosting->client->contacts()->where('is_primary', true)->first();

        if (! $contact?->email) {
            return back()->with('error', 'No hay un contacto con email cargado para recibir el acceso — escribinos y lo resolvemos.');
        }

        $credentialsUrl = URL::temporarySignedRoute(
            'hosting.credentials.show',
            now()->addDays(7),
            ['account' => $account->id],
        );

        try {
            Mail::to($contact->email)->send(new HostingReadyMail($account, $contact, $credentialsUrl));
            ClientEvent::log($hosting->client, 'Cliente reenvió el mail de acceso al hosting desde el portal', \App\Enums\ClientEventType::Hosting, $hosting);

            return back()->with('success', 'Te reenviamos el mail con el link para definir tu contraseña.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No pudimos enviar el mail, intentá de nuevo en unos minutos.');
        }
    }
}
