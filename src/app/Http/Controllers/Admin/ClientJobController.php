<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientEventType;
use App\Enums\JobStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\ClientJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClientJobController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request);

        $job = $client->jobs()->create($validated);

        ClientEvent::log($client, "Se creó el trabajo \"{$job->title}\"", ClientEventType::Job, $job);

        return back()->with('success', 'Trabajo agregado.');
    }

    public function update(Request $request, Client $client, ClientJob $job): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $job->update($this->validated($request));

        return back()->with('success', 'Trabajo actualizado.');
    }

    public function destroy(Client $client, ClientJob $job): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $job->delete();

        return back()->with('success', 'Trabajo eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'project_id'   => 'nullable|exists:projects,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'hours'        => 'nullable|numeric|min:0',
            'amount'       => 'required|numeric|min:0',
            'status'       => ['required', Rule::in(array_column(JobStatus::cases(), 'value'))],
            'requested_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
        ]);
    }
}
