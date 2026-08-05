<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Modules\Odontologia\Models\HistorialOdontologico;
use App\Modules\Odontologia\Models\Odontograma;
use App\Modules\Odontologia\Models\PiezaOdontologica;
use App\Modules\Odontologia\Models\SuperficieOdontologica;
use App\Modules\Odontologia\Models\TratamientoOdontologico;
use App\Modules\Odontologia\OdontogramaPiezaSeeder;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Etapa 4.1: página mínima real (index, sin dominio).
 * Etapa 4.3: primer dominio real — Odontograma + PiezaDental (v1).
 * Etapa 6.6.5 (ver docs/ARQUITECTURA_MODULAR.md): reescrito sobre el
 * modelo de dominio nuevo — Odontograma pasa a ser 1 por paciente (no 1
 * por visita), estado por superficie en vez de por pieza entera,
 * evolución histórica real (HistorialOdontologico) y tratamientos
 * separados del estado (TratamientoOdontologico).
 */
class OdontologiaController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('panel.odontologia.index');
    }

    /**
     * Ya no lista múltiples odontogramas — encuentra o crea el único
     * odontograma vivo de este paciente y va directo a mostrarlo.
     */
    public function porPaciente(Paciente $paciente)
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $odontograma = Odontograma::firstOrCreate(
            ['paciente_id' => $paciente->id],
            ['profesional_id' => auth()->id(), 'fecha' => now()]
        );

        if ($odontograma->wasRecentlyCreated) {
            OdontogramaPiezaSeeder::sembrar($odontograma, 'permanente');
        }

        return redirect()->route('panel.odontologia.show', $odontograma);
    }

    public function show(Odontograma $odontograma)
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $odontograma->load([
            'piezas' => fn ($q) => $q->orderBy('numero_fdi'),
            'piezas.superficies',
            'paciente',
            'tratamientos' => fn ($q) => $q->orderByDesc('created_at'),
            'tratamientos.profesional',
        ]);

        return view('panel.odontologia.show', compact('odontograma'));
    }

    /**
     * Bajo demanda, no automático (Etapa 6.6.5, decisión explícita de
     * Francisco): la mayoría de los pacientes son adultos sin dentición
     * temporal — sembrarla para todos sería 20 filas sin sentido por
     * paciente. Un odontólogo la agrega puntualmente cuando corresponde.
     */
    public function agregarDenticionTemporal(Odontograma $odontograma)
    {
        abort_if(Gate::denies('odontologia_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        OdontogramaPiezaSeeder::sembrar($odontograma, 'temporal');

        return redirect()->route('panel.odontologia.show', $odontograma)
            ->with('success', 'Dentición temporal agregada.');
    }

    /**
     * Click-to-edit sobre una superficie — reemplaza a
     * actualizarPieza() (Etapa 6.6.2), que editaba la pieza entera.
     * Cada cambio de estado real queda en HistorialOdontologico.
     */
    public function actualizarSuperficie(Request $request, SuperficieOdontologica $superficie): JsonResponse
    {
        abort_if(Gate::denies('odontologia_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'estado' => 'required|string|in:' . implode(',', array_keys(SuperficieOdontologica::estadosLabels())),
            'observaciones' => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $superficie->estado;

        $superficie->update($validated);

        if ($estadoAnterior !== $superficie->estado) {
            HistorialOdontologico::registrar(
                'superficie',
                $superficie->id,
                $estadoAnterior,
                $superficie->estado,
                auth()->id(),
            );
        }

        return response()->json([
            'success' => true,
            'superficie_id' => $superficie->id,
            'estado' => $superficie->estado,
            'estado_label' => SuperficieOdontologica::estadosLabels()[$superficie->estado],
            'observaciones' => $superficie->observaciones,
        ]);
    }

    /** Condición de la pieza entera — ausente/extraída, no una cara. */
    public function actualizarPiezaGeneral(Request $request, PiezaOdontologica $pieza): JsonResponse
    {
        abort_if(Gate::denies('odontologia_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'estado_general' => 'nullable|string|in:' . implode(',', array_keys(PiezaOdontologica::estadosGeneralesLabels())),
            'observaciones' => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $pieza->estado_general;

        $pieza->update($validated);

        if ($estadoAnterior !== $pieza->estado_general) {
            HistorialOdontologico::registrar(
                'pieza',
                $pieza->id,
                $estadoAnterior,
                $pieza->estado_general ?? 'sin_condicion',
                auth()->id(),
            );
        }

        return response()->json([
            'success' => true,
            'pieza_id' => $pieza->id,
            'estado_general' => $pieza->estado_general,
            'observaciones' => $pieza->observaciones,
        ]);
    }

    public function crearTratamiento(Request $request, Odontograma $odontograma): JsonResponse
    {
        abort_if(Gate::denies('odontologia_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'numero_fdi' => 'required|integer',
            'superficie' => 'nullable|string|in:' . implode(',', array_keys(SuperficieOdontologica::superficiesLabels())),
            'tipo_tratamiento' => 'required|string|in:' . implode(',', array_keys(TratamientoOdontologico::tiposLabels())),
            'fecha_planificada' => 'nullable|date',
            'material' => 'nullable|string|max:60',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $tratamiento = TratamientoOdontologico::create($validated + [
            'paciente_id' => $odontograma->paciente_id,
            'odontograma_id' => $odontograma->id,
            'profesional_id' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'tratamiento' => $tratamiento->fresh('profesional')]);
    }

    public function completarTratamiento(TratamientoOdontologico $tratamiento): JsonResponse
    {
        abort_if(Gate::denies('odontologia_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tratamiento->marcarRealizado();

        return response()->json(['success' => true, 'tratamiento' => $tratamiento->fresh()]);
    }
}
