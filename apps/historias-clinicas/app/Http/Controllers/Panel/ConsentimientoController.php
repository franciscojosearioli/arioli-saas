<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mail\ConsentimientoFirmaEmail;
use App\Services\NotificacionService;
use App\Models\Consentimiento;
use App\Models\Paciente;
use App\Models\TipoConsentimiento;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ConsentimientoController extends Controller
{
    public function create(Paciente $paciente)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos = TipoConsentimiento::activo()->orderBy('nombre')->get();

        return view('panel.consentimientos.create', compact('paciente', 'tipos'));
    }

    public function store(Request $request, Paciente $paciente)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate(['tipo_id' => 'required|exists:tipos_consentimiento,id']);

        Consentimiento::create([
            'paciente_id' => $paciente->id,
            'tipo_id'     => $request->tipo_id,
        ]);

        return redirect()->route('panel.paciente.show', $paciente->id)
            ->withFragment('consentimientos');
    }

    public function firmarPresencial(Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($consentimiento->firmado_paciente, 403, 'Ya está firmado.');

        $consentimiento->load(['paciente', 'tipo']);

        return view('panel.consentimientos.firmar-presencial', compact('consentimiento'));
    }

    public function guardarFirmaPresencial(Request $request, Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($consentimiento->firmado_paciente, 403, 'Ya está firmado.');

        $request->validate(['firma_canvas_data' => 'required|string']);

        $consentimiento->load(['paciente', 'tipo', 'firmadoPor']);

        $this->savePatientSignature($consentimiento, $request->firma_canvas_data);

        $pac = $consentimiento->paciente;
        NotificacionService::consentimientoFirmadoPaciente(
            $pac->apellido . ', ' . $pac->nombre,
            $pac->id,
            $consentimiento->id,
            auth()->id()
        );

        return redirect()->route('panel.paciente.show', $consentimiento->paciente_id)
            ->withFragment('consentimientos')
            ->with('message', 'Firma del paciente registrada correctamente.');
    }

    // Send email with signing link
    public function enviarEmail(Request $request, Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($consentimiento->firmado_paciente, 403, 'Ya está firmado por el paciente.');

        $request->validate([
            'email'         => 'required|email',
            'expira_horas'  => 'nullable|integer|min:1|max:720',
        ]);

        $consentimiento->load(['paciente', 'tipo']);

        $token = Str::random(64);
        $horas = (int)($request->expira_horas ?? 72);

        $consentimiento->update([
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addHours($horas),
            'token_email'      => $request->email,
        ]);

        try {
            Mail::to($request->email)->send(new ConsentimientoFirmaEmail($consentimiento));
            return back()->with('message', 'Email enviado correctamente a ' . $request->email);
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo enviar el email. Verifique la configuración de correo. Enlace generado: ' . route('consentimiento.firmaPublica', $token));
        }
    }

    // Professional signs with their firma image
    public function firmarProfesional(Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($consentimiento->firmado_profesional, 403, 'Ya tiene firma profesional.');

        /** @var User $user */
        $user = auth()->user();
        abort_if(! $user->firma_imagen, 422, 'No tenés una firma configurada en tu perfil.');

        $consentimiento->load(['paciente', 'tipo']);

        $consentimiento->update([
            'firmado_profesional'    => true,
            'firmado_profesional_at' => Carbon::now(),
            'firmado_por_id'         => $user->id,
        ]);

        $this->regeneratePdf($consentimiento);

        $pac = $consentimiento->paciente;
        NotificacionService::consentimientoFirmadoProfesional(
            $user->name,
            $pac->apellido . ', ' . $pac->nombre,
            $pac->id,
            $consentimiento->id,
            $user->id
        );

        return back()->with('message', 'Firma profesional aplicada correctamente.');
    }

    public function pdf(Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consentimiento->load(['paciente', 'tipo', 'firmadoPor']);

        if ($consentimiento->pdf_path && Storage::disk('public')->exists($consentimiento->pdf_path)) {
            return response()->file(
                Storage::disk('public')->path($consentimiento->pdf_path),
                ['Content-Disposition' => 'inline; filename="consentimiento.pdf"']
            );
        }

        // Generate on-the-fly if no stored PDF
        $pdf = $this->buildPdf($consentimiento);
        return $pdf->stream('consentimiento.pdf');
    }

    public function destroy(Consentimiento $consentimiento)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consentimiento->delete();

        return back()->with('message', 'Consentimiento eliminado.');
    }

    // ── Internal helpers ──────────────────────────────────────────────────

    private function savePatientSignature(Consentimiento $consentimiento, string $canvasData): void
    {
        $raw     = preg_replace('/^data:image\/\w+;base64,/', '', $canvasData);
        $decoded = base64_decode(str_replace(' ', '+', $raw));
        $path    = 'firmas/consentimiento/' . $consentimiento->id . '/paciente.png';
        Storage::disk('public')->put($path, $decoded);

        $consentimiento->update([
            'firmado_paciente'    => true,
            'firmado_paciente_at' => Carbon::now(),
            'firma_paciente_img'  => $path,
        ]);

        $this->regeneratePdf($consentimiento->fresh(['tipo', 'paciente', 'firmadoPor']));
    }

    public function regeneratePdf(Consentimiento $consentimiento): void
    {
        $pdf     = $this->buildPdf($consentimiento);
        $pdfPath = 'consentimientos_doc/' . $consentimiento->id . '/consentimiento.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $consentimiento->update(['pdf_path' => $pdfPath]);
    }

    private function buildPdf(Consentimiento $consentimiento): \Barryvdh\DomPDF\PDF
    {
        $paciente       = $consentimiento->paciente;
        $tipo           = $consentimiento->tipo;
        $firmadoPor     = $consentimiento->firmadoPor;

        $firmaImgPaciente    = null;
        $firmaImgProfesional = null;

        if ($consentimiento->firma_paciente_img && Storage::disk('public')->exists($consentimiento->firma_paciente_img)) {
            $firmaImgPaciente = 'data:image/png;base64,' . base64_encode(
                Storage::disk('public')->get($consentimiento->firma_paciente_img)
            );
        }

        if ($consentimiento->firmado_profesional && $firmadoPor && $firmadoPor->firma_imagen) {
            $firmaProfPath = Storage::disk('public')->path($firmadoPor->firma_imagen);
            if (file_exists($firmaProfPath)) {
                $firmaImgProfesional = 'data:image/png;base64,' . base64_encode(file_get_contents($firmaProfPath));
            }
        }

        return Pdf::loadView('pdf.consentimiento', compact(
            'consentimiento', 'paciente', 'tipo',
            'firmadoPor', 'firmaImgPaciente', 'firmaImgProfesional'
        ));
    }
}
