<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuotaRequest;
use App\Http\Requests\UpdateCuotaRequest;
use App\Http\Resources\CuotaResource;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CuotaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Cuota::class);

        $cuotas = Cuota::query()
            ->with('operacion')
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->whereHas(
                'operacion',
                fn ($sub) => $sub->where('agente_id', $request->user()->id)
            ))
            ->when($request->filled('operacion_id'), fn ($q) => $q->where('operacion_id', $request->integer('operacion_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')))
            ->orderBy('fecha_vencimiento')
            ->paginate();

        return CuotaResource::collection($cuotas);
    }

    public function store(StoreCuotaRequest $request): CuotaResource
    {
        return new CuotaResource(Cuota::create($request->validated()));
    }

    public function show(Cuota $cuota): CuotaResource
    {
        $this->authorize('view', $cuota);

        return new CuotaResource($cuota->load(['operacion', 'pagos']));
    }

    public function update(UpdateCuotaRequest $request, Cuota $cuota): CuotaResource
    {
        $cuota->update($request->validated());

        return new CuotaResource($cuota);
    }

    public function destroy(Cuota $cuota): Response
    {
        $this->authorize('delete', $cuota);

        $cuota->delete();

        return response()->noContent();
    }
}
