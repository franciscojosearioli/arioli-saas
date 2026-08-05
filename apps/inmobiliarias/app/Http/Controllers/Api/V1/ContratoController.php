<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContratoRequest;
use App\Http\Requests\UpdateContratoRequest;
use App\Http\Resources\ContratoResource;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ContratoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Contrato::class);

        $contratos = Contrato::query()
            ->with('operacion')
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->whereHas(
                'operacion',
                fn ($sub) => $sub->where('agente_id', $request->user()->id)
            ))
            ->when($request->filled('operacion_id'), fn ($q) => $q->where('operacion_id', $request->integer('operacion_id')))
            ->orderByDesc('fecha_inicio')
            ->paginate();

        return ContratoResource::collection($contratos);
    }

    public function store(StoreContratoRequest $request): ContratoResource
    {
        return new ContratoResource(Contrato::create($request->validated()));
    }

    public function show(Contrato $contrato): ContratoResource
    {
        $this->authorize('view', $contrato);

        return new ContratoResource($contrato->load('operacion'));
    }

    public function update(UpdateContratoRequest $request, Contrato $contrato): ContratoResource
    {
        $contrato->update($request->validated());

        return new ContratoResource($contrato);
    }

    public function destroy(Contrato $contrato): Response
    {
        $this->authorize('delete', $contrato);

        $contrato->delete();

        return response()->noContent();
    }

    public function renovar(Request $request, Contrato $contrato): ContratoResource
    {
        $this->authorize('update', $contrato);

        $datos = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'clausulas' => ['nullable', 'string'],
        ]);

        return new ContratoResource($contrato->renovar($datos));
    }
}
