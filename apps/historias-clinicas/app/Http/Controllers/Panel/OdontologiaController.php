<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Modules\Odontologia\Models\Odontograma;
use App\Modules\Odontologia\Models\PiezaDental;
use Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Etapa 4.1: página mínima real (index, sin dominio).
 * Etapa 4.3: primer dominio real — Odontograma + PiezaDental. Ver
 * docs/ARQUITECTURA_MODULAR.md para la fricción encontrada al conectar
 * esto con la ficha de Paciente (Historia Clínica).
 */
class OdontologiaController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('panel.odontologia.index');
    }

    public function porPaciente(Paciente $paciente)
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $odontogramas = Odontograma::where('paciente_id', $paciente->id)
            ->orderByDesc('fecha')
            ->get();

        return view('panel.odontologia.paciente', compact('paciente', 'odontogramas'));
    }

    public function crear(Paciente $paciente)
    {
        abort_if(Gate::denies('odontologia_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $odontograma = Odontograma::create([
            'paciente_id' => $paciente->id,
            'profesional_id' => auth()->id(),
            'fecha' => now(),
        ]);

        foreach (Odontograma::numerosFdiAdulto() as $numero) {
            PiezaDental::create([
                'odontograma_id' => $odontograma->id,
                'numero' => $numero,
                'estado' => 'sana',
            ]);
        }

        return redirect()->route('panel.odontologia.show', $odontograma)
            ->with('success', 'Odontograma creado.');
    }

    public function show(Odontograma $odontograma)
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $odontograma->load(['piezas' => fn ($q) => $q->orderBy('numero'), 'paciente']);

        return view('panel.odontologia.show', compact('odontograma'));
    }
}
