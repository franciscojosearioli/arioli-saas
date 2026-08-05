<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use App\Http\Resources\DocumentoResource;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DocumentoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Documento::class);

        $documentos = Documento::query()
            ->with('subidoPor')
            ->when($request->filled('documentable_type'), fn ($q) => $q->where(
                'documentable_type',
                StoreDocumentoRequest::tipoAClase($request->string('documentable_type')->toString())
            ))
            ->when($request->filled('documentable_id'), fn ($q) => $q->where('documentable_id', $request->integer('documentable_id')))
            ->orderByDesc('created_at')
            ->paginate();

        return DocumentoResource::collection($documentos);
    }

    public function store(StoreDocumentoRequest $request): DocumentoResource
    {
        $documento = $request->documentableClass()::findOrFail($request->validated('documentable_id'))
            ->documentos()
            ->create([
                ...$request->safe()->except(['documentable_type', 'documentable_id']),
                'subido_por_id' => $request->user()->id,
            ]);

        return new DocumentoResource($documento);
    }

    public function show(Documento $documento): DocumentoResource
    {
        $this->authorize('view', $documento);

        return new DocumentoResource($documento->load('subidoPor'));
    }

    public function update(UpdateDocumentoRequest $request, Documento $documento): DocumentoResource
    {
        $documento->update($request->validated());

        return new DocumentoResource($documento);
    }

    public function destroy(Documento $documento): Response
    {
        $this->authorize('delete', $documento);

        $documento->delete();

        return response()->noContent();
    }
}
