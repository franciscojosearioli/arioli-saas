<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConstructoraRequest;
use App\Http\Requests\UpdateConstructoraRequest;
use App\Http\Resources\ConstructoraResource;
use App\Models\Constructora;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ConstructoraController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Constructora::class);

        return ConstructoraResource::collection(
            Constructora::withCount('desarrollos')->paginate()
        );
    }

    public function store(StoreConstructoraRequest $request): ConstructoraResource
    {
        return new ConstructoraResource(Constructora::create($request->validated()));
    }

    public function show(Constructora $constructora): ConstructoraResource
    {
        $this->authorize('view', $constructora);

        return new ConstructoraResource($constructora->loadCount('desarrollos'));
    }

    public function update(UpdateConstructoraRequest $request, Constructora $constructora): ConstructoraResource
    {
        $constructora->update($request->validated());

        return new ConstructoraResource($constructora);
    }

    public function destroy(Constructora $constructora): Response
    {
        $this->authorize('delete', $constructora);

        $constructora->delete();

        return response()->noContent();
    }
}
