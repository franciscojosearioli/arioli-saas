<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesarrolloRequest;
use App\Http\Requests\UpdateDesarrolloRequest;
use App\Http\Resources\DesarrolloResource;
use App\Models\Desarrollo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DesarrolloController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Desarrollo::class);

        $desarrollos = Desarrollo::query()
            ->with('constructora')
            ->withCount('propiedades')
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->string('tipo')))
            ->when($request->filled('constructora_id'), fn ($q) => $q->where('constructora_id', $request->integer('constructora_id')))
            ->paginate();

        return DesarrolloResource::collection($desarrollos);
    }

    public function store(StoreDesarrolloRequest $request): DesarrolloResource
    {
        return new DesarrolloResource(Desarrollo::create($request->validated()));
    }

    public function show(Desarrollo $desarrollo): DesarrolloResource
    {
        $this->authorize('view', $desarrollo);

        return new DesarrolloResource($desarrollo->load('constructora')->loadCount('propiedades'));
    }

    public function update(UpdateDesarrolloRequest $request, Desarrollo $desarrollo): DesarrolloResource
    {
        $desarrollo->update($request->validated());

        return new DesarrolloResource($desarrollo);
    }

    public function destroy(Desarrollo $desarrollo): Response
    {
        $this->authorize('delete', $desarrollo);

        $desarrollo->delete();

        return response()->noContent();
    }
}
