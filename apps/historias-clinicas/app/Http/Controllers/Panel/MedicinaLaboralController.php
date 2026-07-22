<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Modules\MedicinaLaboral\Models\EvaluacionLaboral;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Etapa 5 — segundo Componente real, mismo patrón que
 * Panel/OdontologiaController. Diferencia real encontrada acá: a
 * diferencia del odontograma (una fotografía fija de 32 piezas sin input
 * del usuario), una Evaluación Laboral SÍ necesita un formulario real
 * (tipo/fecha/estado/observaciones) — no hay snapshot por defecto posible.
 */
class MedicinaLaboralController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('medicina_laboral_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('panel.medicina-laboral.index');
    }

    public function porPaciente(Paciente $paciente)
    {
        abort_if(Gate::denies('medicina_laboral_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $evaluaciones = EvaluacionLaboral::where('paciente_id', $paciente->id)
            ->orderByDesc('fecha')
            ->get();

        return view('panel.medicina-laboral.paciente', compact('paciente', 'evaluaciones'));
    }

    public function crear(Request $request, Paciente $paciente)
    {
        abort_if(Gate::denies('medicina_laboral_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $datos = $request->validate([
            'tipo' => 'required|in:preocupacional,periodico,egreso',
            'fecha' => 'required|date',
            'estado' => 'required|in:apto,no_apto,apto_con_restricciones',
            'observaciones' => 'nullable|string',
        ]);

        EvaluacionLaboral::create([
            'paciente_id' => $paciente->id,
            'profesional_id' => auth()->id(),
            ...$datos,
        ]);

        return redirect()->route('panel.medicina-laboral.paciente', $paciente)
            ->with('success', 'Evaluación laboral registrada.');
    }
}
