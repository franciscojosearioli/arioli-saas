<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperacionRequest;
use App\Http\Requests\UpdateOperacionRequest;
use App\Http\Resources\OperacionResource;
use App\Models\Cliente;
use App\Models\Operacion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class OperacionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Operacion::class);

        $operaciones = Operacion::query()
            ->with(['propiedad', 'agente'])
            ->withCount('cuotas')
            // §07: un agente solo trabaja sus propias operaciones.
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->where('agente_id', $request->user()->id))
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->string('tipo')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')))
            ->orderByDesc('fecha_inicio')
            ->paginate();

        return OperacionResource::collection($operaciones);
    }

    public function store(StoreOperacionRequest $request): OperacionResource
    {
        $datos = $request->validated();

        // §07: si no viene agente_id y quien la abre es un agente, es su
        // propia operación — sin esto, OperacionPolicy::esDueno() la deja
        // invisible para su propio creador apenas se guarda (mismo caso
        // que Lead en Fase 1).
        if (! isset($datos['agente_id']) && $request->user()->hasRole('agente')) {
            $datos['agente_id'] = $request->user()->id;
        }

        return new OperacionResource(Operacion::create($datos));
    }

    public function show(Operacion $operacion): OperacionResource
    {
        $this->authorize('view', $operacion);

        return new OperacionResource(
            $operacion->load(['propiedad', 'agente', 'partes', 'comision'])->loadCount('cuotas')
        );
    }

    public function update(UpdateOperacionRequest $request, Operacion $operacion): OperacionResource
    {
        $operacion->update($request->validated());

        return new OperacionResource($operacion);
    }

    public function destroy(Operacion $operacion): Response
    {
        $this->authorize('delete', $operacion);

        $operacion->delete();

        return response()->noContent();
    }

    public function asignarParte(Request $request, Operacion $operacion): OperacionResource
    {
        $this->authorize('update', $operacion);

        $datos = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'rol' => ['required', Rule::in(['comprador', 'vendedor', 'locador', 'locatario', 'garante'])],
        ]);

        $operacion->asignarParte(Cliente::findOrFail($datos['cliente_id']), $datos['rol']);

        return new OperacionResource($operacion->load('partes'));
    }

    public function generarPlanDeCuotas(Request $request, Operacion $operacion): OperacionResource
    {
        $this->authorize('update', $operacion);

        $datos = $request->validate([
            'cantidad_cuotas' => ['required', 'integer', 'min:1', 'max:360'],
            'fecha_primer_vencimiento' => ['required', 'date'],
            'monto_por_cuota' => ['required', 'numeric', 'min:0.01'],
        ]);

        $operacion->generarPlanDeCuotas(
            $datos['cantidad_cuotas'],
            $datos['fecha_primer_vencimiento'],
            (string) $datos['monto_por_cuota'],
        );

        return new OperacionResource($operacion->loadCount('cuotas'));
    }

    public function cerrar(Operacion $operacion): OperacionResource
    {
        $this->authorize('update', $operacion);

        $operacion->cerrar();

        return new OperacionResource($operacion->fresh()->load(['propiedad', 'comision']));
    }

    public function cancelar(Operacion $operacion): OperacionResource
    {
        $this->authorize('update', $operacion);

        $operacion->cancelar();

        return new OperacionResource($operacion->fresh());
    }
}
