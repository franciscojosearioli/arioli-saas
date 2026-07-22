<?php

namespace App\Http\Controllers\Panel;


use App\Http\Controllers\Controller;
use App\Http\Requests\Medicacion\MassDestroyMedicacionRequest;
use App\Http\Requests\Medicacion\StoreMedicacionRequest;
use App\Http\Requests\Medicacion\UpdateMedicacionRequest;
use App\Models\Medicacion;
use App\Models\Paciente;
use App\Models\Receta;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class MedicacionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('medicacion_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Obtener una medicación por paciente, seleccionando la fecha más reciente
        $medicaciones = Medicacion::whereHas('paciente', function($query) {
            $query->whereHas('ficha_admision', function($query) {
                $query->whereNull('fecha_egreso');
            });
        })
        ->select('paciente_id', \DB::raw('MAX(fecha) as fecha'))
        ->groupBy('paciente_id')
        ->with('paciente')
        ->get()
        ->map(function ($item) {
            // Obtener el ID de una medicación para usar en la ruta "show"
            $medicacion = Medicacion::where('paciente_id', $item->paciente_id)
                ->where('fecha', $item->fecha)
                ->first();
            return [
                'id' => $medicacion ? $medicacion->id : null,
                'paciente' => $item->paciente,
                'fecha' => $item->fecha
            ];
        })
        ->filter(function ($item) {
            return !is_null($item['id']); // Filtrar registros sin ID válido
        });

        return view('panel.medicacion.index', compact('medicaciones'));
    }

    public function create()
    {
        abort_if(Gate::denies('medicacion_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pacientesDatos = Paciente::where('status', 1)->orderBy('apellido')->get();

        $pacientes = $pacientesDatos->mapWithKeys(function ($paciente) {
            return [$paciente->id => $paciente->apellido . ', ' . $paciente->nombre];
        })->prepend(trans('global.pleaseSelect'), '');

        return view('panel.medicacion.create', compact('pacientes'));
    }

    public function store(StoreMedicacionRequest $request)
    {
        // Obtener los datos del formulario
        $data = $request->all();

        // Procesar cada conjunto de datos para medicamento
        foreach ($data['medicamento'] as $key => $medicamento) {
            // Crear un nuevo registro para cada fila
            Medicacion::create([
                'paciente_id' => $data['paciente_id'],
                'fecha'       => $data['fecha'],
                'medicamento' => $medicamento,
                'unidad'      => $data['unidad'][$key],
                'horario'     => $data['horario'][$key],
                'user_id'     => auth()->id(),
            ]);
        }
        
        notify()->success('Se ha cargado correctamente la medicacion.', 'Nuevo dato cargado');

        if ($request->filled('from_paciente')) {
            return redirect()->route('panel.paciente.show', $request->from_paciente)
                ->with('success', 'Medicación cargada exitosamente.');
        }

        return redirect()->route('panel.medicacion.index');
    }
    public function edit($paciente_id, $fecha)
{
    abort_if(Gate::denies('medicacion_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    // Validate fecha format
    try {
        Carbon::parse($fecha);
    } catch (\Exception $e) {
        abort(400, 'Invalid date format');
    }

    $sample = Medicacion::where('paciente_id', $paciente_id)->where('fecha', $fecha)->first();
    abort_if(
        $sample && !auth()->user()->ownsOrAdmin($sample, 'user_id'),
        Response::HTTP_FORBIDDEN,
        'Solo podés modificar prescripciones que hayas cargado vos.'
    );

    // Obtener el paciente
    $paciente = Paciente::findOrFail($paciente_id);

    // Obtener todas las medicaciones del paciente para la fecha específica, ordenadas por horario
    $medicaciones = Medicacion::where('paciente_id', $paciente_id)
        ->where('fecha', $fecha)
        ->whereNotNull('fecha')
        ->orderByRaw("FIELD(horario, 'Mañana', 'Mediodía', 'Tarde', 'Noche')")
        ->get();

    // Preparar datos para el select de pacientes
    $pacientesDatos = Paciente::all();
    $pacientes = $pacientesDatos->mapWithKeys(function ($paciente) {
        return [$paciente->id => $paciente->apellido . ', ' . $paciente->nombre];
    })->prepend(trans('global.pleaseSelect'), '');

    return view('panel.medicacion.edit', compact('paciente', 'medicaciones', 'pacientes', 'fecha'));
}

public function update(UpdateMedicacionRequest $request, $paciente_id, $fecha)
{
    $sample = Medicacion::where('paciente_id', $paciente_id)->where('fecha', $fecha)->first();
    abort_if(
        $sample && !auth()->user()->ownsOrAdmin($sample, 'user_id'),
        Response::HTTP_FORBIDDEN,
        'Solo podés modificar prescripciones que hayas cargado vos.'
    );

    // Eliminar las medicaciones existentes para la fecha y paciente
    Medicacion::where('paciente_id', $paciente_id)
        ->where('fecha', $fecha)
        ->delete();

    // Procesar los nuevos datos del formulario solo si existen
    $data = $request->all();

    if ($request->has('medicamento') && !empty(array_filter($request->medicamento))) {
        foreach ($data['medicamento'] as $key => $medicamento) {
            // Solo crear si el medicamento no está vacío y existen las claves correspondientes
            if (!empty($medicamento) && isset($data['unidad'][$key]) && isset($data['horario'][$key])) {
                Medicacion::create([
                    'paciente_id' => $paciente_id,
                    'fecha' => $fecha,
                    'medicamento' => $medicamento,
                    'unidad' => $data['unidad'][$key],
                    'horario' => $data['horario'][$key],
                ]);
            }
        }
    }

    notify()->success('Se ha actualizado correctamente la medicación.', 'Datos actualizados');

    if ($request->filled('from_paciente')) {
        return redirect()->route('panel.paciente.show', $request->from_paciente)
            ->with('success', 'Medicación actualizada exitosamente.');
    }

    return redirect()->route('panel.medicacion.index');
}

    
    
    public function show($id, Request $request)
    {
        abort_if(Gate::denies('medicacion_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicacion = Medicacion::with('paciente')->findOrFail($id);
        $pacienteId = $medicacion->paciente_id;

        // Todas las medicaciones del paciente
        $allMedicaciones = Medicacion::where('paciente_id', $pacienteId)
            ->whereHas('paciente', function ($query) {
                $query->whereHas('ficha_admision', function ($query) {
                    $query->whereNull('fecha_egreso');
                });
            })
            ->whereNotNull('fecha')
            ->orderBy('fecha', 'desc')
            ->orderByRaw("FIELD(horario, 'Mañana', 'Mediodía', 'Tarde', 'Noche')")
            ->get();

        $groupedByFecha = $allMedicaciones->groupBy(fn($item) =>
            Carbon::parse($item->fecha)->format('Y-m-d')
        );

        $ultimaFecha             = $groupedByFecha->keys()->first();
        $medicacionesUltimaFecha = $groupedByFecha[$ultimaFecha]->groupBy('horario');
        $medicacionesAnteriores  = $groupedByFecha->filter(fn($v, $k) => $k !== $ultimaFecha);

        // Recetas del paciente (via informes) con filtros opcionales
        $recetaQuery = Receta::with(['informe.tipo'])
            ->whereHas('informe', fn($q) => $q->where('paciente_id', $pacienteId));

        if ($request->filled('rec_informe')) {
            $recetaQuery->where('informe_id', $request->rec_informe);
        }
        if ($request->filled('rec_desde')) {
            $recetaQuery->whereHas('informe', fn($q) =>
                $q->where('fecha', '>=', $request->rec_desde)
            );
        }
        if ($request->filled('rec_hasta')) {
            $recetaQuery->whereHas('informe', fn($q) =>
                $q->where('fecha', '<=', $request->rec_hasta)
            );
        }

        $recetas = $recetaQuery->orderByDesc(
            \App\Models\Informe::select('fecha')
                ->whereColumn('informes.id', 'recetas.informe_id')
                ->limit(1)
        )->get();

        // Informes del paciente que tienen recetas (para el select de filtro)
        $informesConRecetas = \App\Models\Informe::with('tipo')
            ->where('paciente_id', $pacienteId)
            ->whereHas('recetas')
            ->orderByDesc('fecha')
            ->get();

        return view('panel.medicacion.show', compact(
            'medicacion',
            'medicacionesUltimaFecha',
            'ultimaFecha',
            'medicacionesAnteriores',
            'recetas',
            'informesConRecetas'
        ));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('medicacion_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Medicacion = Medicacion::findOrFail($id);

        abort_if(
            !auth()->user()->ownsOrAdmin($Medicacion, 'user_id'),
            Response::HTTP_FORBIDDEN,
            'Solo podés eliminar prescripciones que hayas cargado vos.'
        );

        $Medicacion->delete();

        return back();
    }

    public function massDestroy(MassDestroyMedicacionRequest $request)
    {
        $Medicaciones = Medicacion::find(request('ids'));

        foreach ($Medicaciones as $Medicacion) {
            $Medicacion->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}



