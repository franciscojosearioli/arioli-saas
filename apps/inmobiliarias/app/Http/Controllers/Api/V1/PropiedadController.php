<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropiedadRequest;
use App\Http\Requests\UpdatePropiedadRequest;
use App\Http\Resources\PropiedadResource;
use App\Models\Propiedad;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PropiedadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Propiedad::class);

        $propiedades = Propiedad::query()
            ->with(['desarrollo', 'propietario'])
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->string('tipo')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')))
            ->when($request->filled('moneda'), fn ($q) => $q->where('moneda', $request->string('moneda')))
            ->when($request->filled('desarrollo_id'), fn ($q) => $q->where('desarrollo_id', $request->integer('desarrollo_id')))
            ->paginate();

        return PropiedadResource::collection($propiedades);
    }

    public function store(StorePropiedadRequest $request): PropiedadResource
    {
        return new PropiedadResource(Propiedad::create($request->validated()));
    }

    public function show(Propiedad $propiedad): PropiedadResource
    {
        $this->authorize('view', $propiedad);

        return new PropiedadResource($propiedad->load(['desarrollo', 'propietario']));
    }

    public function update(UpdatePropiedadRequest $request, Propiedad $propiedad): PropiedadResource
    {
        $propiedad->update($request->validated());

        return new PropiedadResource($propiedad);
    }

    public function destroy(Propiedad $propiedad): Response
    {
        $this->authorize('delete', $propiedad);

        $propiedad->delete();

        return response()->noContent();
    }
}
