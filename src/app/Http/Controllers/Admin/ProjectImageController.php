<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProjectImageController extends Controller
{
    public function store(Request $request, Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'image' => 'required|image|max:4096',
            'title' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store("projects/{$project->id}/images", 'public');

        $project->images()->create([
            'path'     => $path,
            'title'    => $validated['title'] ?? null,
            'position' => ($project->images()->max('position') ?? 0) + 1,
        ]);

        return back()->with('success', 'Imagen agregada.');
    }

    public function destroy(Client $client, Project $project, ProjectImage $image): RedirectResponse
    {
        Gate::authorize('manage-clients');

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
