<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePublicacionRequest;
use App\Http\Resources\PublicacionCanalResource;
use App\Http\Resources\PublicacionResource;
use App\Models\OutboxEvent;
use App\Models\Propiedad;
use App\Models\Publicacion;
use App\Models\PublicacionCanal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicacionController extends Controller
{
    public function store(Request $request, Propiedad $propiedad): PublicacionResource
    {
        $this->authorize('create', Publicacion::class);

        $publicacion = Publicacion::firstOrCreate(['propiedad_id' => $propiedad->id]);

        return new PublicacionResource($publicacion->load('canales'));
    }

    public function show(Publicacion $publicacion): PublicacionResource
    {
        $this->authorize('view', $publicacion);

        return new PublicacionResource($publicacion->load(['propiedad', 'canales']));
    }

    public function update(UpdatePublicacionRequest $request, Publicacion $publicacion): PublicacionResource
    {
        $publicacion->update($request->validated());

        return new PublicacionResource($publicacion->load('canales'));
    }

    public function activarCanal(Request $request, Publicacion $publicacion): PublicacionResource
    {
        $this->authorize('update', $publicacion);

        $datos = $request->validate([
            'canal' => ['required', Rule::in(['marketplace', 'sitio_web'])],
        ]);

        $publicacion->activarCanal($datos['canal']);
        $this->encolarSincronizacion($publicacion);

        return new PublicacionResource($publicacion->load('canales'));
    }

    public function pausarCanal(PublicacionCanal $publicacionCanal): PublicacionCanalResource
    {
        $this->authorize('update', $publicacionCanal->publicacion);

        $publicacionCanal->pausar();

        return new PublicacionCanalResource($publicacionCanal);
    }

    public function despublicarCanal(PublicacionCanal $publicacionCanal): PublicacionCanalResource
    {
        $this->authorize('update', $publicacionCanal->publicacion);

        $publicacionCanal->despublicar();
        $this->encolarSincronizacion($publicacionCanal->publicacion);

        return new PublicacionCanalResource($publicacionCanal);
    }

    public function reintentarCanal(PublicacionCanal $publicacionCanal): PublicacionCanalResource
    {
        $this->authorize('update', $publicacionCanal->publicacion);

        $publicacionCanal->update(['estado' => 'borrador', 'ultimo_error' => null]);
        $this->encolarSincronizacion($publicacionCanal->publicacion);

        return new PublicacionCanalResource($publicacionCanal);
    }

    private function encolarSincronizacion(Publicacion $publicacion): void
    {
        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $publicacion->propiedad_id,
            'evento' => 'PublicacionActualizada',
            'payload' => ['propiedad_id' => $publicacion->propiedad_id],
        ]);
    }
}
