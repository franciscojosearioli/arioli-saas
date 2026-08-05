<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientPortalUserController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'client_id' => $client->id,
            'password'  => Hash::make(Str::random(40)),
        ]);

        Password::broker('clientes')->sendResetLink(['email' => $user->email]);

        return back()->with('success', 'Acceso al portal creado. Le enviamos un email para que defina su contraseña.');
    }

    public function resend(Client $client, User $user): RedirectResponse
    {
        Gate::authorize('manage-clients');

        abort_if($user->client_id !== $client->id, 404);

        Password::broker('clientes')->sendResetLink(['email' => $user->email]);

        return back()->with('success', 'Reenviamos el email de acceso al portal.');
    }

    public function destroy(Client $client, User $user): RedirectResponse
    {
        Gate::authorize('manage-clients');

        abort_if($user->client_id !== $client->id, 404);

        $user->delete();

        return back()->with('success', 'Acceso al portal eliminado.');
    }
}
