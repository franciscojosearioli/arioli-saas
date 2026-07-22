<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Receta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('informe_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Receta::with(['informe.paciente', 'informe.tipo', 'informe.profesional'])
            ->whereHas('informe');

        if ($request->filled('fecha_desde')) {
            $query->whereHas('informe', fn($q) =>
                $q->where('fecha', '>=', $request->fecha_desde)
            );
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereHas('informe', fn($q) =>
                $q->where('fecha', '<=', $request->fecha_hasta)
            );
        }

        if ($request->filled('paciente')) {
            $search = $request->paciente;
            $query->whereHas('informe.paciente', fn($q) =>
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
            );
        }

        $recetas = $query->orderByDesc(
            \App\Models\Informe::select('fecha')
                ->whereColumn('informes.id', 'recetas.informe_id')
                ->limit(1)
        )->paginate(25)->withQueryString();

        return view('panel.recetas.index', compact('recetas'));
    }

    public function destroy(Receta $receta)
    {
        abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $informe = $receta->informe;
        abort_if(
            !auth()->user()->ownsOrAdmin($informe, 'profesional_id'),
            Response::HTTP_FORBIDDEN,
            'Solo podés eliminar recetas de informes propios.'
        );
        abort_if($informe->firmado, 403, 'El informe está firmado y no puede modificarse.');

        Storage::disk('public')->delete($receta->archivo);
        $receta->delete();

        return back()->with('message', 'Receta eliminada.');
    }
}
