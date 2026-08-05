<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Lead::class);

        $leads = Lead::query()
            ->with(['agente', 'cliente', 'propiedad'])
            // §07: un agente solo trabaja su propia cartera de leads; el
            // resto de los roles con acceso al listado ven la cartera
            // completa del equipo (admin ya pasa por Gate::before).
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->where('agente_id', $request->user()->id))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')))
            ->when($request->filled('origen'), fn ($q) => $q->where('origen', $request->string('origen')))
            ->paginate();

        return LeadResource::collection($leads);
    }

    public function store(StoreLeadRequest $request): LeadResource
    {
        return new LeadResource(Lead::create($request->validated()));
    }

    public function show(Lead $lead): LeadResource
    {
        $this->authorize('view', $lead);

        return new LeadResource($lead->load(['agente', 'cliente', 'propiedad']));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): LeadResource
    {
        $lead->update($request->validated());

        return new LeadResource($lead);
    }

    public function destroy(Lead $lead): Response
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return response()->noContent();
    }
}
