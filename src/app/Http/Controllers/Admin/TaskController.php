<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'client_id' => $client->id,
            'title'     => $validated['title'],
            'due_date'  => $validated['due_date'] ?? null,
            'status'    => 'pendiente',
            'source'    => 'manual',
        ]);

        return back()->with('success', 'Tarea agregada.');
    }

    public function toggle(Client $client, Task $task): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $task->update([
            'status' => $task->isCompleted() ? 'pendiente' : 'completada',
        ]);

        return back()->with('success', 'Tarea actualizada.');
    }

    public function destroy(Client $client, Task $task): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $task->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
