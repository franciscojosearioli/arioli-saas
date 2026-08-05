<?php

namespace App\Http\Controllers;

use App\Models\HostingAccount;
use App\Models\User;
use App\Services\Hosting\HostingPanelManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Pantalla pública firmada (temporarySignedRoute) donde el cliente define su
 * propia contraseña — nunca se le manda la contraseña generada por mail.
 * Mismo principio de confianza que ya usa SignatureController. Esta
 * contraseña es la de su Panel de Cliente (cliente.arioli.dev): además de
 * actualizar el password real de HestiaCP, crea (o reutiliza) el `User` de
 * portal del contacto y lo loguea directo, para no duplicar altas manuales
 * vía Admin\ClientPortalUserController.
 */
class HostingCredentialController extends Controller
{
    public function show(Request $request, HostingAccount $account)
    {
        abort_unless($request->hasValidSignature(), 403);

        if ($account->credential_claimed_at) {
            return view('hosting.credentials-claimed');
        }

        return view('hosting.credentials', [
            'account'  => $account,
            'username' => $account->remote_username,
        ]);
    }

    public function claim(Request $request, HostingAccount $account)
    {
        abort_unless($request->hasValidSignature(), 403);

        if ($account->credential_claimed_at) {
            return view('hosting.credentials-claimed');
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = HostingPanelManager::driver()->changePassword($account->remote_username, $validated['password']);

        if (! $result->success) {
            return back()->withErrors(['password' => $result->message ?? 'No pudimos cambiar la contraseña. Contactá a soporte.']);
        }

        $credential = $account->credentials()->first();
        $credential?->update(['secret' => $validated['password']]);

        $account->update(['credential_claimed_at' => now()]);

        $client = $account->hosting->client;
        $contact = $client->contacts()->where('is_primary', true)->first();

        if ($contact?->email) {
            $user = User::where('email', $contact->email)->first();

            if ($user && $user->client_id !== null && $user->client_id !== $client->id) {
                // Email ya usado por el portal de otro cliente — no se pisa,
                // se deja el mensaje genérico de "listo" sin loguear a nadie.
                return view('hosting.credentials-claimed');
            }

            $user ??= new User(['client_id' => $client->id]);
            $user->fill([
                'name'      => $contact->name,
                'email'     => $contact->email,
                'client_id' => $client->id,
                'password'  => Hash::make($validated['password']),
            ]);
            $user->email_verified_at ??= now();
            $user->save();

            Auth::guard('cliente')->login($user);

            return redirect()->route('cliente.dashboard');
        }

        return view('hosting.credentials-claimed');
    }
}
