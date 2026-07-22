<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Panel\ConsentimientoController;
use App\Models\Consentimiento;
use App\Services\NotificacionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FirmaPublicaController extends Controller
{
    public function show(string $token)
    {
        $consentimiento = Consentimiento::with(['paciente', 'tipo'])
            ->where('token', $token)
            ->firstOrFail();

        if ($consentimiento->firmado_paciente) {
            return view('firma-publica.ya-firmado', compact('consentimiento'));
        }

        if (! $consentimiento->tokenValido()) {
            return view('firma-publica.token-expirado', compact('consentimiento'));
        }

        return view('firma-publica.show', compact('consentimiento'));
    }

    public function guardar(Request $request, string $token)
    {
        $consentimiento = Consentimiento::with(['paciente', 'tipo', 'firmadoPor'])
            ->where('token', $token)
            ->firstOrFail();

        if ($consentimiento->firmado_paciente) {
            return view('firma-publica.ya-firmado', compact('consentimiento'));
        }

        if (! $consentimiento->tokenValido()) {
            return view('firma-publica.token-expirado', compact('consentimiento'));
        }

        $request->validate(['firma_canvas_data' => 'required|string']);

        // Save signature image
        $raw     = preg_replace('/^data:image\/\w+;base64,/', '', $request->firma_canvas_data);
        $decoded = base64_decode(str_replace(' ', '+', $raw));
        $path    = 'firmas/consentimiento/' . $consentimiento->id . '/paciente.png';
        Storage::disk('public')->put($path, $decoded);

        $consentimiento->update([
            'firmado_paciente'    => true,
            'firmado_paciente_at' => Carbon::now(),
            'firma_paciente_img'  => $path,
            'token'               => null,
        ]);

        // Regenerate PDF with embedded signature
        $ctrl = new ConsentimientoController();
        $freshCons = $consentimiento->fresh(['tipo', 'paciente', 'firmadoPor']);
        $ctrl->regeneratePdf($freshCons);

        // Notify staff
        $pac = $freshCons->paciente;
        NotificacionService::consentimientoFirmadoPaciente(
            $pac->apellido . ', ' . $pac->nombre,
            $pac->id,
            $freshCons->id
        );

        return view('firma-publica.firmado-ok', compact('consentimiento'));
    }
}
