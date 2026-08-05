<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactRole;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientContactController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'role'       => ['required', Rule::in(array_column(ContactRole::cases(), 'value'))],
            'is_primary' => 'boolean',
        ]);

        $client->contacts()->create([
            ...$validated,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('success', 'Contacto agregado.');
    }

    public function update(Request $request, Client $client, ClientContact $contact): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'role'       => ['required', Rule::in(array_column(ContactRole::cases(), 'value'))],
            'is_primary' => 'boolean',
        ]);

        $contact->update([
            ...$validated,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('success', 'Contacto actualizado.');
    }

    public function destroy(Client $client, ClientContact $contact): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $contact->delete();

        return back()->with('success', 'Contacto eliminado.');
    }
}
