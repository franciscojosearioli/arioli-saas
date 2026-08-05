<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountUsersController extends Controller
{
    /**
     * Tope simple para evitar abuso del alta self-service — el admin puede
     * crear más desde Admin\ClientPortalUserController si un cliente lo
     * necesita de verdad.
     */
    private const MAX_USERS_PER_CLIENT = 5;

    public function index(): View
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with('portalUsers')->firstOrFail();

        return view('cliente.account.users', [
            'currentUserId' => $user->id,
            'portalUsers'   => $client->portalUsers,
            'canAddMore'    => $client->portalUsers->count() < self::MAX_USERS_PER_CLIENT,
            'maxUsers'      => self::MAX_USERS_PER_CLIENT,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id, 404);

        $client = $user->client()->with('portalUsers')->firstOrFail();

        if ($client->portalUsers->count() >= self::MAX_USERS_PER_CLIENT) {
            return back()->with('error', 'Llegaste al máximo de ' . self::MAX_USERS_PER_CLIENT . ' usuarios para esta cuenta — escribinos si necesitás más.');
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ]);

        $newUser = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'client_id' => $client->id,
            'password'  => Hash::make(Str::random(40)),
        ]);

        Password::broker('clientes')->sendResetLink(['email' => $newUser->email]);

        return back()->with('success', 'Invitamos a ' . $newUser->name . ' por email para que defina su contraseña.');
    }

    public function destroy(User $portalUser): RedirectResponse
    {
        Gate::authorize('client-access');

        $user = Auth::guard('cliente')->user();

        abort_if(! $user->client_id || $portalUser->client_id !== $user->client_id, 404);

        if ($portalUser->id === $user->id) {
            return back()->with('error', 'No podés eliminar tu propio acceso desde acá.');
        }

        $portalUser->delete();

        return back()->with('success', 'Le quitamos el acceso al portal a ' . $portalUser->name . '.');
    }
}
