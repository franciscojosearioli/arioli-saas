<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate(['name' => 'required|string|max:100']);

        $tag = Tag::firstOrCreate(['name' => $validated['name']]);

        $client->tags()->syncWithoutDetaching([$tag->id]);

        return back()->with('success', 'Etiqueta agregada.');
    }

    public function destroy(Client $client, Tag $tag): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $client->tags()->detach($tag->id);

        return back()->with('success', 'Etiqueta quitada.');
    }
}
