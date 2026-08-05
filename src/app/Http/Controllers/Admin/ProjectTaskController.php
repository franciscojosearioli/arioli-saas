<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectTaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectTaskController extends Controller
{
    public function store(Request $request, Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $lastPosition = $project->tasks()->where('status', ProjectTaskStatus::Todo->value)->max('position') ?? -1;

        $project->tasks()->create([
            ...$validated,
            'status'   => ProjectTaskStatus::Todo,
            'position' => $lastPosition + 1,
        ]);

        return back()->with('success', 'Tarea agregada.');
    }

    public function update(Request $request, Client $client, Project $project, ProjectTask $task): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return back()->with('success', 'Tarea actualizada.');
    }

    public function updateStatus(Request $request, Client $client, Project $project, ProjectTask $task): JsonResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'status'   => ['required', Rule::in(array_column(ProjectTaskStatus::cases(), 'value'))],
            'position' => 'required|integer|min:0',
        ]);

        $task->update($validated);

        return response()->json(['success' => true, 'message' => 'Tarea actualizada.']);
    }

    public function destroy(Client $client, Project $project, ProjectTask $task): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $task->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
