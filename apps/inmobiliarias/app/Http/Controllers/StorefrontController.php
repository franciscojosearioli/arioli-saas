<?php

namespace App\Http\Controllers;

use App\Models\Constructora;
use App\Models\Desarrollo;
use App\Models\Propiedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * §08 (Rev. 1.3): storefront público embebido en el subdominio del propio
 * tenant — lee su misma base, filtrada a lo publicado. Sin outbox, sin
 * segunda base, sin índice de búsqueda: a la escala de un solo tenant,
 * MySQL con estos filtros alcanza (ver §15/§16 del Artifact).
 */
class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only([
            'q', 'provincia', 'ciudad', 'tipo', 'tipo_operacion', 'precio_min', 'precio_max', 'ambientes',
        ]);

        $propiedades = Propiedad::query()
            ->whereHas('publicacion')
            ->when(! empty($filtros['q']), fn ($q) => $q->where('titulo', 'like', '%'.$filtros['q'].'%'))
            ->when(! empty($filtros['provincia']), fn ($q) => $q->where('provincia', $filtros['provincia']))
            ->when(! empty($filtros['ciudad']), fn ($q) => $q->where('ciudad', 'like', '%'.$filtros['ciudad'].'%'))
            ->when(! empty($filtros['tipo']), fn ($q) => $q->where('tipo', $filtros['tipo']))
            ->when(! empty($filtros['ambientes']), fn ($q) => $q->where('ambientes', '>=', (int) $filtros['ambientes']))
            ->when(! empty($filtros['precio_min']), fn ($q) => $q->where('precio', '>=', (float) $filtros['precio_min']))
            ->when(! empty($filtros['precio_max']), fn ($q) => $q->where('precio', '<=', (float) $filtros['precio_max']))
            ->when(! empty($filtros['tipo_operacion']), fn ($q) => $q->whereHas(
                'operaciones',
                fn ($oq) => $oq->where('tipo', $filtros['tipo_operacion'])->where('estado', 'abierta')
            ))
            ->with(['desarrollo', 'fotos'])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('storefront.index', ['propiedades' => $propiedades, 'filtros' => $filtros]);
    }

    public function propiedad(Propiedad $propiedad): View
    {
        abort_unless($propiedad->publicacion()->exists(), 404);

        return view('storefront.propiedad', [
            'propiedad' => $propiedad->load(['fotos', 'desarrollo', 'operaciones' => fn ($q) => $q->where('estado', 'abierta')]),
        ]);
    }

    /**
     * §08: mapa interactivo con todas las unidades coloreadas por estado —
     * mismo patrón que loteos, esta vez sobre la base del propio tenant.
     */
    public function desarrollo(Desarrollo $desarrollo): View
    {
        $esMysql = DB::connection()->getDriverName() === 'mysql';

        $totalUnidades = $desarrollo->propiedades()->whereHas('publicacion')->count();

        $unidadesQuery = $desarrollo->propiedades()->whereHas('publicacion');
        if ($esMysql) {
            $unidadesQuery->addSelect(DB::raw('ST_AsGeoJSON(ubicacion) as geojson'));
        }

        $unidades = $unidadesQuery->get()->map(fn (Propiedad $propiedad) => [
            'id' => $propiedad->id,
            'titulo' => $propiedad->titulo,
            'estado' => $propiedad->estado,
            'precio' => $propiedad->precio,
            'moneda' => $propiedad->moneda,
            'url' => route('storefront.propiedad', $propiedad),
            'coordenadas' => ($esMysql && $propiedad->geojson) ? json_decode($propiedad->geojson) : null,
        ])->filter(fn (array $unidad) => $unidad['coordenadas'] !== null)->values();

        return view('storefront.desarrollo', [
            'desarrollo' => $desarrollo,
            'poligonoGeneral' => $desarrollo->ubicacionComoGeoJson(),
            'unidades' => $unidades,
            'totalUnidades' => $totalUnidades,
        ]);
    }

    public function constructora(Constructora $constructora): View
    {
        return view('storefront.constructora', [
            'constructora' => $constructora->load('desarrollos'),
        ]);
    }
}
