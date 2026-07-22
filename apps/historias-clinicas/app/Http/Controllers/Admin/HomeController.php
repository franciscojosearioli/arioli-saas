<?php

namespace App\Http\Controllers\Admin;

use App\Models\Paciente;
use App\Models\Medicacion;
use App\Models\Informe;
use App\Models\User;
use App\Models\ConfiguracionSistema;
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

        // Medicaciones de pacientes activos
        $medicaciones = Medicacion::whereHas('paciente', function($query) {
            $query->whereHas('ficha_admision', function($query) {
                $query->whereNull('fecha_egreso');
            });
        })->get();

        // Informes del mes actual
        $informesDelMes = Informe::whereMonth('created_at', now()->month)->count();

        // Total de usuarios del sistema
        $totalUsuarios = User::count();

        // Estadísticas de actividad (últimos 30 días)
        $informesRecientes = Informe::where('created_at', '>=', now()->subDays(30))->count();
        $medicacionesRecientes = Medicacion::where('created_at', '>=', now()->subDays(30))->count();
        $pacientesRecientes = Paciente::where('created_at', '>=', now()->subDays(30))->count();

        // Últimos pacientes registrados
        $ultimosPacientes = Paciente::with('ficha_admision')->orderBy('created_at', 'desc')->take(5)->get();

        // Alertas del usuario
        $alerts = Auth::user()->userUserAlerts()->withPivot('read')->limit(10)->orderBy('created_at', 'ASC')->get()->reverse();

        return view('admin.dashboard.home', compact(
            'pacientesActivosCount',
            'historiasCount',
            'medicaciones',
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