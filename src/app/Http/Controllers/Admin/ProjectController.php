<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientEventType;
use App\Enums\Priority;
use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => ['required', Rule::in(array_column(ProjectType::cases(), 'value'))],
            'priority'                => ['required', Rule::in(array_column(Priority::cases(), 'value'))],
            'domain_id'               => 'nullable|exists:client_domains,id',
            'hosting_id'              => 'nullable|exists:hostings,id',
            'ssl_certificate_id'      => 'nullable|exists:ssl_certificates,id',
            'cloudflare_service_id'   => 'nullable|exists:cloudflare_services,id',
            'license_id'              => 'nullable|exists:licenses,id',
            'production_url'          => 'nullable|string|max:255',
            'staging_url'             => 'nullable|string|max:255',
            'public_name'             => 'nullable|string|max:255',
            'commercial_description'  => 'nullable|string',
            'problem_solved'          => 'nullable|string',
            'key_features'            => 'nullable|string',
            'display_order'           => 'nullable|integer',
            'is_featured'             => 'boolean',
            'show_publicly'           => 'boolean',
        ]);

        $validated['is_featured']   = $request->boolean('is_featured');
        $validated['show_publicly'] = $request->boolean('show_publicly');
        $validated['key_features']  = $this->keyFeaturesToArray($request->input('key_features'));

        $project = $client->projects()->create($validated);

        ClientEvent::log($client, "Se creó el proyecto \"{$project->name}\"", ClientEventType::Project, $project);

        return redirect()->route('clients.show', $client)->with('success', 'Proyecto creado.');
    }

    public function show(Client $client, Project $project)
    {
        Gate::authorize('manage-clients');

        $project->load(['domain', 'hosting', 'sslCertificate', 'cloudflareService', 'license.plan.product', 'repositories', 'technologies', 'services', 'owner', 'notes.user', 'documents.user', 'credentials', 'tags']);
        $technologies = Technology::orderBy('name')->get();

        return view('admin.clients.projects.show', compact('client', 'project', 'technologies'));
    }

    public function update(Request $request, Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => ['required', Rule::in(array_column(ProjectType::cases(), 'value'))],
            'priority'                => ['required', Rule::in(array_column(Priority::cases(), 'value'))],
            'status'                  => 'required|in:active,inactive,archived',
            'domain_id'               => 'nullable|exists:client_domains,id',
            'hosting_id'              => 'nullable|exists:hostings,id',
            'ssl_certificate_id'      => 'nullable|exists:ssl_certificates,id',
            'cloudflare_service_id'   => 'nullable|exists:cloudflare_services,id',
            'license_id'              => 'nullable|exists:licenses,id',
            'production_url'          => 'nullable|string|max:255',
            'staging_url'             => 'nullable|string|max:255',
            'started_at'              => 'nullable|date',
            'delivered_at'            => 'nullable|date',
            'archived_at'             => 'nullable|date',
            'public_name'             => 'nullable|string|max:255',
            'commercial_description'  => 'nullable|string',
            'problem_solved'          => 'nullable|string',
            'key_features'            => 'nullable|string',
            'display_order'           => 'nullable|integer',
            'is_featured'             => 'boolean',
            'show_publicly'           => 'boolean',
        ]);

        $validated['is_featured']   = $request->boolean('is_featured');
        $validated['show_publicly'] = $request->boolean('show_publicly');
        $validated['key_features']  = $this->keyFeaturesToArray($request->input('key_features'));

        $project->update($validated);

        return back()->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $project->delete();

        return redirect()->route('clients.show', $client)->with('success', 'Proyecto eliminado.');
    }

    public function syncTechnologies(Request $request, Client $client, Project $project): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate(['technologies' => 'array', 'technologies.*' => 'exists:technologies,id']);

        $project->technologies()->sync($validated['technologies'] ?? []);

        return back()->with('success', 'Tecnologías actualizadas.');
    }

    private function keyFeaturesToArray(?string $raw): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", (string) $raw))));
    }
}
