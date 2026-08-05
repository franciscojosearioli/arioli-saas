<?php

namespace App\Http\Controllers;

use App\Models\Desarrollo;
use App\Models\FichaPropiedad;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DesarrolloController extends Controller
{
    /**
     * §08: "mapa interactivo (Leaflet) con todas las unidades/lotes
     * coloreadas por estado — exactamente el patrón que ya funciona en
     * loteos hoy". Una sola query con ST_AsGeoJSON en el select (no una
     * por unidad) — mismo patrón que HomeController::mostrarMapa de
     * loteos.
     */
    public function show(Desarrollo $desarrollo): View
    {
        $esMysql = DB::connection()->getDriverName() === 'mysql';

        // Total real de unidades publicadas (independiente de si ya
        // tienen forma cargada) — no se puede derivar de $unidades, que
        // solo cuenta las que el mapa efectivamente puede dibujar.
        $totalUnidades = FichaPropiedad::where('desarrollo_id', $desarrollo->id)->count();

        $unidadesQuery = FichaPropiedad::where('desarrollo_id', $desarrollo->id);

        if ($esMysql) {
            $unidadesQuery->addSelect(DB::raw('ST_AsGeoJSON(ubicacion) as geojson'));
        }

        $unidades = $unidadesQuery->get()->map(fn (FichaPropiedad $ficha) => [
            'id' => $ficha->id,
            'slug' => $ficha->slug,
            'titulo' => $ficha->titulo,
            'estado' => $ficha->estado,
            'precio' => $ficha->precio,
            'moneda' => $ficha->moneda,
            'coordenadas' => ($esMysql && $ficha->geojson) ? json_decode($ficha->geojson) : null,
        ])->filter(fn (array $unidad) => $unidad['coordenadas'] !== null)->values();

        return view('desarrollos.show', [
            'desarrollo' => $desarrollo,
            'poligonoGeneral' => $desarrollo->ubicacionComoGeoJson(),
            'unidades' => $unidades,
            'totalUnidades' => $totalUnidades,
        ]);
    }
}
