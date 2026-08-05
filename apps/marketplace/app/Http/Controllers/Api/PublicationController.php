<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\FichaPropiedad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PublicationController extends Controller
{
    public function store(StorePublicationRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $ficha = FichaPropiedad::publicar(
            $datos['tenant_id'],
            $datos['propiedad_id'],
            collect($datos)->except(['tenant_id', 'propiedad_id'])->all(),
        );

        return response()->json(['id' => (string) $ficha->id, 'slug' => $ficha->slug], 201);
    }

    public function show(FichaPropiedad $publication): JsonResponse
    {
        return response()->json($publication);
    }

    public function update(UpdatePublicationRequest $request, FichaPropiedad $publication): JsonResponse
    {
        // No un update() plano: 'desarrollo_id' (se resuelve a la fila
        // local de `desarrollos`) y 'ubicacion_wkt' no son fillable a
        // propósito, así que un update() directo los ignoraría en
        // silencio en cada republicación — publicar() es la única forma
        // correcta de escribirlos, tanto en alta como en edición.
        $ficha = FichaPropiedad::publicar($publication->tenant_id, $publication->propiedad_id, $request->validated());

        return response()->json(['id' => (string) $ficha->id, 'slug' => $ficha->slug]);
    }

    public function destroy(FichaPropiedad $publication): Response
    {
        $publication->delete();

        return response()->noContent();
    }
}
