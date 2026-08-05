<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClienteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Cliente::class);

        $clientes = Cliente::query()
            ->withCount('propiedades')
            ->when($request->filled('tipo_persona'), fn ($q) => $q->where('tipo_persona', $request->string('tipo_persona')))
            ->paginate();

        return ClienteResource::collection($clientes);
    }

    public function store(StoreClienteRequest $request): ClienteResource
    {
        return new ClienteResource(Cliente::create($request->validated()));
    }

    public function show(Cliente $cliente): ClienteResource
    {
        $this->authorize('view', $cliente);

        return new ClienteResource($cliente->loadCount('propiedades'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): ClienteResource
    {
        $cliente->update($request->validated());

        return new ClienteResource($cliente);
    }

    public function destroy(Cliente $cliente): Response
    {
        $this->authorize('delete', $cliente);

        $cliente->delete();

        return response()->noContent();
    }
}
