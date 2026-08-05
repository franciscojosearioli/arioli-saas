<?php

namespace App\Http\Controllers\Admin;

use App\Models\Paciente;
use App\Models\Medicacion;
use App\Models\Informe;
use App\Models\User;
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

        // Total de historias clínicas (pacientes registrados)
        $historiasCount = Paciente::count();

        // Medicaciones de pacientes activos — mismo Gate que el link de
        // menú "Prescripciones" (AuthGates: medicacion_access ↔
        // capability_key='medicacion'). Sin este chequeo el dashboard
        // consultaba y mostraba Medicación aunque el perfil del tenant la
        // tuviera deshabilitada (ej. Odontología, ver Ajustes post-6.5).
        $capabilityMedicacion = app(PlatformRegistry::class)->isCapabilityEnabled('medicacion');

        $medicaciones = $capabilityMedicacion
            ? Medicacion::whereHas('paciente', function($query) {
                $query->whereHas('ficha_admision', function($query) {
                    $query->whereNull('fecha_egreso');
                });
            })->get()
            : collect();

        // Informes del mes actual
        $informesDelMes = Informe::whereMonth('created_at', now()->month)->count();

        // Total de usuarios del sistema
        $totalUsuarios = User::count();

        // Estadísticas de actividad (últimos 30 días)
        $informesRecientes = Informe::where('created_at', '>=', now()->subDays(30))->count();
        $medicacionesRecientes = $capabilityMedicacion
            ? Medicacion::where('created_at', '>=', now()->subDays(30))->count()
            : 0;
        $pacientesRecientes = Paciente::where('created_at', '>=', now()->subDays(30))->count();

        // Últimos pacientes registrados
        $ultimosPacientes = Paciente::with('ficha_admision')->orderBy('created_at', 'desc')->take(5)->get();

        // Alertas del usuario
        $alerts = Auth::user()->userUserAlerts()->withPivot('read')->limit(10)->orderBy('created_at', 'ASC')->get()->reverse();

        return view('admin.dashboard.home', compact(
            'pacientesActivosCount',
            'historiasCount',
            'medicaciones',
            'capabilityMedicacion',
            'informesDelMes',
            'totalUsuarios',
            'informesRecientes',
            'medicacionesRecientes',
            'pacientesRecientes',
            'ultimosPacientes',
            'alerts'
        ));
    }
}