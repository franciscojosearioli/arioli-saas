<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectRepositoryController extends Controller
{
    public function store(Request $request, Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'provider' => 'required|in:github,gitlab,bitbucket,privado,otro',
            'url'      => 'required|string|max:255',
            'branch'   => 'nullable|string|max:100',
        ]);

        $project->repositories()->create([
            ...$validated,
            'is_main'    => $request->boolean('is_main'),
            'is_private' => $request->boolean('is_private', true),
        ]);

        return back()->with('success', 'Repositorio agregado.');
    }

    public function destroy(Client $client, Project $project, ProjectRepository $repository): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $repository->delete();

        return back()->with('success', 'Repositorio eliminado.');
    }
}
