<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagoRequest;
use App\Http\Resources\PagoResource;
use App\Models\Cuota;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PagoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Pago::class);

        $pagos = Pago::query()
            ->with(['cuota.operacion', 'registradoPor'])
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->whereHas(
                'cuota.operacion',
                fn ($sub) => $sub->where('agente_id', $request->user()->id)
            ))
            ->when($request->filled('cuota_id'), fn ($q) => $q->where('cuota_id', $request->integer('cuota_id')))
            ->orderByDesc('fecha')
            ->paginate();

        return PagoResource::collection($pagos);
    }

    public function store(StorePagoRequest $request): PagoResource
    {
        $cuota = Cuota::findOrFail($request->validated('cuota_id'));

        $pago = $cuota->registrarPago([
            ...$request->safe()->except('cuota_id'),
            'registrado_por_id' => $request->user()->id,
        ]);

        return new PagoResource($pago);
    }

    public function show(Pago $pago): PagoResource
    {
        $this->authorize('view', $pago);

        return new PagoResource($pago->load(['cuota', 'registradoPor']));
    }
}
