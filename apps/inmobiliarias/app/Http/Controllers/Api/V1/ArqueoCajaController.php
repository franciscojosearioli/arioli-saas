<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArqueoCajaRequest;
use App\Http\Resources\ArqueoCajaResource;
use App\Models\ArqueoCaja;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArqueoCajaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ArqueoCaja::class);

        $arqueos = ArqueoCaja::query()
            ->with('cerradoPor')
            ->orderByDesc('fecha')
            ->paginate();

        return ArqueoCajaResource::collection($arqueos);
    }

    public function store(StoreArqueoCajaRequest $request): ArqueoCajaResource
    {
        $arqueo = ArqueoCaja::create([
            ...$request->validated(),
            'monto_esperado' => ArqueoCaja::calcularEsperado($request->validated('fecha')),
            'cerrado_por_id' => $request->user()->id,
        ]);

        return new ArqueoCajaResource($arqueo);
    }

    public function show(ArqueoCaja $arqueoCaja): ArqueoCajaResource
    {
        $this->authorize('view', $arqueoCaja);

        return new ArqueoCajaResource($arqueoCaja->load('cerradoPor'));
    }
}
