<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDomain;
use App\Models\ClientJob;
use App\Models\ClientService;
use App\Models\Hosting;
use App\Models\Note;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
{
    private const NOTEABLE_TYPES = [
        'client'  => Client::class,
        'domain'  => ClientDomain::class,
        'hosting' => Hosting::class,
        'project' => Project::class,
        'job'     => ClientJob::class,
        'service' => ClientService::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'noteable_type' => 'required|in:client,domain,hosting,project,job,service',
            'noteable_id'   => 'required|integer',
            'body'          => 'required|string',
            'is_pinned'     => 'boolean',
        ]);

        $modelClass = self::NOTEABLE_TYPES[$validated['noteable_type']];
        $noteable   = $modelClass::findOrFail($validated['noteable_id']);

        Note::create([
            'noteable_type' => $modelClass,
            'noteable_id'   => $noteable->id,
            'body'          => $validated['body'],
            'is_pinned'     => $request->boolean('is_pinned'),
            'user_id'       => Auth::id(),
        ]);

        return back()->with('success', 'Nota agregada.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $note->delete();

        return back()->with('success', 'Nota eliminada.');
    }
}
