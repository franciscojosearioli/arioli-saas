<?php

namespace App\Http\Controllers;

use App\Models\FichaPropiedad;
use Illuminate\Http\Request;
use Illuminate\View\View;

// §08: "buscar por ubicación/tipo/precio/operación/ambientes" — filtros
// simples combinables, no una búsqueda facetada completa todavía.
// Builder::where() es agnóstico de motor (Meilisearch en real, el driver
// 'collection' en tests) — evita acoplar este controller a sintaxis de
// filtro específica de Meilisearch.
class BusquedaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only([
            'q', 'provincia', 'ciudad', 'tipo_operacion', 'tipo_propiedad', 'precio_min', 'precio_max',
        ]);

        $busqueda = FichaPropiedad::search($filtros['q'] ?? '');

        foreach (['provincia', 'ciudad', 'tipo_operacion', 'tipo_propiedad'] as $campo) {
            if (! empty($filtros[$campo])) {
                $busqueda->where($campo, $filtros[$campo]);
            }
        }

        if (! empty($filtros['precio_min'])) {
            $busqueda->where('precio', '>=', (float) $filtros['precio_min']);
        }
        if (! empty($filtros['precio_max'])) {
            $busqueda->where('precio', '<=', (float) $filtros['precio_max']);
        }

        $fichas = $busqueda->paginate(20)->withQueryString();

        return view('busqueda.index', [
            'fichas' => $fichas,
            'filtros' => $filtros,
        ]);
    }
}
