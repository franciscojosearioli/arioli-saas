<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agenda\StoreAgendaRequest;
use App\Http\Requests\Agenda\UpdateAgendaRequest;
use App\Models\Agenda;
use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('agenda_access'), 403);

        $pacientes     = Paciente::orderBy('apellido')->orderBy('nombre')->get(['id', 'nombre', 'apellido']);
        $profesionales = User::orderBy('name')->get(['id', 'name']);
        $estados       = Agenda::estadosLabels();
        $modalidades   = Agenda::modalidadesLabels();

        return view('panel.agenda.index', compact('pacientes', 'profesionales', 'estados', 'modalidades'));
    }

    public function eventos(Request $request): JsonResponse
    {
        abort_if(Gate::denies('agenda_access'), 403);

        $inicio = $request->get('start') ? Carbon::parse($request->get('start')) : Carbon::now()->startOfMonth();
        $fin    = $request->get('end')   ? Carbon::parse($request->get('end'))   : Carbon::now()->endOfMonth();

        $query = Agenda::with(['paciente', 'profesional'])
            ->whereBetween('fecha_hora_inicio', [$inicio, $fin]);

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('profesional_id')) {
            $query->where('profesional_id', $request->profesional_id);
        }
        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->modalidad);
        }

        $eventos = $query->get()->map(function (Agenda $agenda) {
            $estadoLabel = Agenda::estadosLabels()[$agenda->estado] ?? $agenda->estado;
            $modalLabel  = Agenda::modalidadesLabels()[$agenda->modalidad] ?? $agenda->modalidad;
            $paciente    = $agenda->paciente;

            return [
                'id'        => $agenda->id,
                'title'     => "{$paciente->apellido}, {$paciente->nombre}",
                'start'     => $agenda->fecha_hora_inicio->format('Y-m-d\TH:i:s'),
                'end'       => $agenda->fecha_hora_fin->format('Y-m-d\TH:i:s'),
                'color'     => $agenda->getColorEstado(),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'paciente'    => "{$paciente->nombre} {$paciente->apellido}",
                    'profesional' => $agenda->getNombreProfesional(),
                    'motivo'      => $agenda->motivo,
                    'modalidad'   => $modalLabel,
                    'estado'      => $estadoLabel,
                    'estado_key'  => $agenda->estado,
                    'url_show'    => route('panel.agenda.show', $agenda->id),
                    'url_edit'    => route('panel.agenda.edit', $agenda->id),
                ],
            ];
        });

        return response()->json($eventos);
    }

    public function create()
    {
        abort_if(Gate::denies('agenda_create'), 403);

        $user           = auth()->user();
        $pacientes      = Paciente::where('status', 1)->orderBy('apellido')->orderBy('nombre')->get();
        $profesionales  = $user->is_admin
            ? User::orderBy('name')->get()
            : User::where('id', $user->id)->get();
        $especialidades = Especialidad::orderBy('nombre')->get();
        $estados        = Agenda::estadosLabels();
        $modalidades    = Agenda::modalidadesLabels();

        return view('panel.agenda.create', compact('pacientes', 'profesionales', 'especialidades', 'estados', 'modalidades'));
    }

    public function store(StoreAgendaRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();

        if ($data['profesional_tipo'] === 'registrado') {
            $data['profesional_externo_nombre'] = null;
            $data['profesional_externo_email']  = null;
            if (!auth()->user()->is_admin) {
                $data['profesional_id'] = auth()->id();
            }
        } else {
            $data['profesional_id'] = null;
        }

        if ($data['modalidad'] === 'presencial') {
            $data['link_virtual'] = null;
        }

        Agenda::create($data);

        if ($request->filled('from_paciente')) {
            return redirect()->route('panel.paciente.show', $request->from_paciente)
                ->with('message', 'Cita agendada exitosamente.');
        }

        return redirect()->route('panel.agenda.index')
            ->with('message', 'Cita agendada exitosamente.');
    }

    public function show(Agenda $agenda)
    {
        abort_if(Gate::denies('agenda_show'), 403);

        $agenda->load(['paciente', 'profesional', 'creadoPor']);

        return view('panel.agenda.show', compact('agenda'));
    }

    private function canManageAgenda(Agenda $agenda): bool
    {
        $user = auth()->user();
        return $agenda->creado_por === $user->id
            || $agenda->profesional_id === $user->id;
    }

    public function edit(Agenda $agenda)
    {
        abort_if(Gate::denies('agenda_edit'), 403);
        abort_if(!$this->canManageAgenda($agenda), 403, 'Solo podés modificar citas en las que participás.');

        $user           = auth()->user();
        $pacientes      = Paciente::orderBy('apellido')->orderBy('nombre')->get();
        $profesionales  = $user->is_admin
            ? User::orderBy('name')->get()
            : User::where('id', $user->id)->get();
        $especialidades = Especialidad::orderBy('nombre')->get();
        $estados        = Agenda::estadosLabels();
        $modalidades    = Agenda::modalidadesLabels();

        return view('panel.agenda.edit', compact('agenda', 'pacientes', 'profesionales', 'especialidades', 'estados', 'modalidades'));
    }

    public function update(UpdateAgendaRequest $request, Agenda $agenda)
    {
        abort_if(!$this->canManageAgenda($agenda), 403, 'Solo podés modificar citas en las que participás.');

        $data = $request->validated();

        if ($data['profesional_tipo'] === 'registrado') {
            $data['profesional_externo_nombre'] = null;
            $data['profesional_externo_email']  = null;
            if (!auth()->user()->is_admin) {
                $data['profesional_id'] = auth()->id();
            }
        } else {
            $data['profesional_id'] = null;
        }

        if ($data['modalidad'] === 'presencial') {
            $data['link_virtual'] = null;
        }

        DB::transaction(function () use ($data, $agenda) {
            $fresh = Agenda::lockForUpdate()->findOrFail($agenda->id);

            // Resetear recordatorio si cambia la fecha (evaluado con datos frescos del lock)
            if ($fresh->fecha_hora_inicio->format('Y-m-d H:i') !== Carbon::parse($data['fecha_hora_inicio'])->format('Y-m-d H:i')) {
                $data['recordatorio_enviado'] = false;
            }

            $agenda->update($data);
        });

        if ($request->filled('from_paciente')) {
            return redirect()->route('panel.paciente.show', $request->from_paciente)
                ->with('message', 'Cita actualizada exitosamente.');
        }

        return redirect()->route('panel.agenda.show', $agenda->id)
            ->with('message', 'Cita actualizada exitosamente.');
    }

    public function destroy(Agenda $agenda)
    {
        abort_if(Gate::denies('agenda_delete'), 403);
        abort_if(!$this->canManageAgenda($agenda), 403, 'Solo podés eliminar citas en las que participás.');

        $agenda->delete();

        return redirect()->route('panel.agenda.index')
            ->with('message', 'Cita eliminada exitosamente.');
    }

    public function citasPorPaciente(\App\Models\Paciente $paciente): JsonResponse
    {
        $citas = Agenda::where('paciente_id', $paciente->id)
            ->orderBy('fecha_hora_inicio', 'desc')
            ->limit(30)
            ->get(['id', 'fecha_hora_inicio', 'motivo'])
            ->map(fn($c) => [
                'id'    => $c->id,
                'label' => Carbon::parse($c->fecha_hora_inicio)->format('d/m/Y H:i') . ' — ' . Str::limit($c->motivo, 50),
            ]);

        return response()->json($citas);
    }

    public function cambiarEstado(Request $request, Agenda $agenda): JsonResponse
    {
        abort_if(Gate::denies('agenda_edit'), 403);
        abort_if(!$this->canManageAgenda($agenda), 403, 'Solo podés modificar citas en las que participás.');

        $request->validate(['estado' => 'required|in:pendiente,confirmado,cancelado,realizado']);

        DB::transaction(function () use ($request, $agenda) {
            Agenda::lockForUpdate()->findOrFail($agenda->id);
            $agenda->update(['estado' => $request->estado]);
        });

        return response()->json([
            'success' => true,
            'estado'  => $agenda->estado,
            'color'   => $agenda->getColorEstado(),
            'label'   => Agenda::estadosLabels()[$agenda->estado],
        ]);
    }
}
