<?php

namespace App\Http\Controllers\Panel;

use App\Models\Agenda;
use App\Models\Paciente;
use App\Models\Medicacion;
use App\Models\Informe;
use App\Models\ConfiguracionSistema;
use App\Platform\PlatformRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class HomeController
{
    public function index()
    {
        // Configuración del sistema
        $sistemaConfig = ConfiguracionSistema::first();
        View::share('sistemaConfig', $sistemaConfig);

        // Pacientes activos (con ficha de admisión sin fecha de egreso)
        $pacientesActivosCount = Paciente::whereHas('ficha_admision', function($query) {
            $query->whereNull('fecha_egreso');
        })->count();

        // Pacientes inactivos (con fecha de egreso)
        $pacientesInactivosCount = Paciente::whereHas('ficha_admision', function($query) {
            $query->whereNotNull('fecha_egreso');
        })->count();

        // Total de historias clínicas
        $historiasCount = Paciente::count();

        // Medicación — mismo Gate que el link de menú "Prescripciones"
        // (AuthGates: medicacion_access ↔ capability_key='medicacion'). Sin
        // este chequeo el dashboard consultaba y mostraba Medicación aunque
        // el perfil del tenant la tuviera deshabilitada (ej. Odontología).
        $capabilityMedicacion = app(PlatformRegistry::class)->isCapabilityEnabled('medicacion');

        $medicaciones = collect();
        if ($capabilityMedicacion) {
            $pacientes = Paciente::whereHas('ficha_admision', function($query) {
                $query->whereNull('fecha_egreso');
            })->get();

            foreach ($pacientes as $paciente) {
                $ultimaFecha = Medicacion::where('paciente_id', $paciente->id)
                    ->latest('fecha')
                    ->value('fecha');

                if ($ultimaFecha) {
                    $meds = Medicacion::where('paciente_id', $paciente->id)
                        ->where('fecha', $ultimaFecha)
                        ->get();
                    $medicaciones = $medicaciones->merge($meds);
                }
            }
        }

        // Conteos adicionales
        $informesCount = Informe::count();
        $medicacionesCount = $capabilityMedicacion ? Medicacion::count() : 0;

        // Estadísticas adicionales para el dashboard
        $informesHoy = Informe::whereDate('created_at', now())->count();
        $informesSemana = Informe::where('created_at', '>=', now()->subDays(7))->count();
        $medicacionesHoy = $medicaciones->count();

        // Últimos pacientes registrados (activos)
        $ultimosPacientes = Paciente::whereHas('ficha_admision', function($query) {
            $query->whereNull('fecha_egreso');
        })->with('ficha_admision')->orderBy('created_at', 'desc')->take(5)->get();

        // Últimos informes
        $ultimosInformes = Informe::with('paciente')->orderBy('created_at', 'desc')->take(5)->get();

        // Alertas del usuario
        $alerts = Auth::user()->userUserAlerts()->withPivot('read')->limit(10)->orderBy('created_at', 'ASC')->get()->reverse();

        // Próximas citas del profesional logueado
        $proximasCitas = Agenda::with(['paciente'])
            ->where(function ($q) {
                $q->where('profesional_id', auth()->id())
                  ->orWhere('creado_por', auth()->id());
            })
            ->whereIn('estado', ['pendiente', 'confirmado', 'cancelado'])
            ->where('fecha_hora_inicio', '>=', now()->startOfDay())
            ->orderBy('fecha_hora_inicio')
            ->take(10)
            ->get();

        return view('panel.home', compact(
            'pacientesActivosCount',
            'pacientesInactivosCount',
            'historiasCount',
            'informesCount',
            'capabilityMedicacion',
            'medicacionesCount',
            'informesHoy',
            'informesSemana',
            'medicacionesHoy',
            'medicaciones',
            'ultimosPacientes',
            'ultimosInformes',
            'alerts',
            'proximasCitas'
        ));
    }
}