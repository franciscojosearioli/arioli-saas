<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComisionResource;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ComisionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comision::class);

        $comisiones = Comision::query()
            ->with(['agente', 'operacion'])
            ->when($request->user()->hasRole('agente'), fn ($q) => $q->where('agente_id', $request->user()->id))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')))
            ->orderByDesc('created_at')
            ->paginate();

        return ComisionResource::collection($comisiones);
    }

    public function show(Comision $comision): ComisionResource
    {
        $this->authorize('view', $comision);

        return new ComisionResource($comision->load(['agente', 'operacion']));
    }

    public function liquidar(Comision $comision): ComisionResource
    {
        $this->authorize('update', $comision);

        $comision->liquidar();

        return new ComisionResource($comision->fresh());
    }
}
