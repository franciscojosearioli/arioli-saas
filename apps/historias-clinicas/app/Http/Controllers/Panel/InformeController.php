<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Informe\MassDestroyInformeRequest;
use App\Http\Requests\Informe\StoreInformeRequest;
use App\Http\Requests\Informe\UpdateInformeRequest;
use App\Models\Agenda;
use App\Models\FirmaAuditoria;
use App\Models\Informe;
use App\Models\InformeTipo;
use App\Models\Paciente;
use App\Models\PlantillaDocumento;
use App\Models\PlantillaDocumentoVersion;
use App\Models\Receta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InformeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('informe_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informes = Informe::with(['paciente', 'tipo', 'profesional'])->get();
        $tiposDeInforme = InformeTipo::all();

        return view('panel.informes.index', compact('Informes', 'tiposDeInforme'));
    }

    public function create()
    {
        abort_if(Gate::denies('informe_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos = InformeTipo::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pacientesDatos = Paciente::where('status', 1)->orderBy('apellido')->get();
        $pacientes = $pacientesDatos->mapWithKeys(function ($paciente) {
            return [$paciente->id => $paciente->apellido . ', ' . $paciente->nombre];
        })->prepend(trans('global.pleaseSelect'), '');

        $profesionales = User::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $agendas = collect();

        return view('panel.informes.create', compact('tipos', 'pacientes', 'profesionales', 'agendas'));
    }

    /**
     * AJAX: plantillas activas de un tipo de informe. Motor de documentos
     * (ver docs/ARQUITECTURA_MODULAR.md) — mismo Gate que el resto del
     * módulo, no agrega un permiso nuevo.
     */
    public function plantillasPorTipo($tipoId)
    {
        abort_if(Gate::denies('informe_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $plantillas = PlantillaDocumento::where('tipo_documento_id', $tipoId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id', 'nombre']);

        return response()->json($plantillas);
    }

    /**
     * AJAX: renderiza la versión vigente de una plantilla con las variables
     * disponibles en el formulario en ese momento (paciente, diagnóstico,
     * fecha, profesional). El profesional puede seguir editando el
     * resultado antes de guardar — es un punto de partida, no un bloqueo.
     */
    public function plantillaPreview($plantillaId, Request $request)
    {
        abort_if(Gate::denies('informe_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $plantilla = PlantillaDocumento::findOrFail($plantillaId);
        $version   = $plantilla->versionVigente();

        if (! $version) {
            return response()->json(['error' => 'Esta plantilla no tiene una versión vigente.'], 404);
        }

        $paciente    = $request->filled('paciente_id') ? Paciente::find($request->input('paciente_id')) : null;
        $profesional = $request->filled('profesional_id') ? User::find($request->input('profesional_id')) : null;

        Carbon::setLocale('es');

        $variables = [
            'paciente_nombre'    => $paciente ? trim($paciente->apellido . ', ' . $paciente->nombre) : '',
            'diagnostico'        => $request->input('diagnostico', ''),
            'fecha'              => $request->filled('fecha') ? Carbon::parse($request->input('fecha'))->translatedFormat('d \d\e F \d\e Y') : '',
            'profesional_nombre' => $profesional->name ?? '',
        ];

        return response()->json([
            'version_id' => $version->id,
            'contenido'  => $version->renderizar($variables),
        ]);
    }

    /**
     * Valida que la versión de plantilla enviada realmente pertenezca al
     * tipo de informe seleccionado (defensa en profundidad — evita guardar
     * una versión de un tipo distinto por un formulario manipulado).
     */
    private function resolverPlantillaVersionId(?string $plantillaVersionId, int $tipoId): ?int
    {
        if (! $plantillaVersionId) {
            return null;
        }

        $version = PlantillaDocumentoVersion::with('plantilla')->find($plantillaVersionId);

        if (! $version || (int) $version->plantilla->tipo_documento_id !== $tipoId) {
            return null;
        }

        return $version->id;
    }

    public function store(StoreInformeRequest $request)
    {
        try {
            $request->validate([
                'document_file.*' => 'nullable|mimes:pdf|max:10240',
                'receta_file.*'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            ]);

            $documento = new Informe();
            $documento->paciente_id    = $request->input('paciente_id');
            $documento->tipo_id        = $request->input('tipo_id');
            $documento->profesional_id = $request->input('profesional_id') ?: null;
            $documento->agenda_id      = $request->input('agenda_id') ?: null;
            $documento->fecha          = $request->input('fecha');
            $documento->diagnostico    = $request->input('diagnostico');
            $documento->codigo_cie10   = $request->input('codigo_cie10');

            $tipoSeleccion = $request->input('tipo_seleccion');
            $firmarAhora   = $request->input('firmar_ahora') === '1';

            $paciente    = Paciente::findOrFail($request->input('paciente_id'));
            $tipoInforme = InformeTipo::findOrFail($request->input('tipo_id'));
            Carbon::setLocale('es');
            $fechaFormateada = Carbon::parse($request->input('fecha'))->translatedFormat('d \d\e F \d\e Y');

            $profesionalObj = $request->input('profesional_id')
                ? User::find($request->input('profesional_id'))
                : null;

            if ($tipoSeleccion === 'redaccion') {
                $documento->redaccion      = $request->input('redaccion_informe');
                $documento->document_files = null;
                $documento->plantilla_documento_version_id = $this->resolverPlantillaVersionId(
                    $request->input('plantilla_documento_version_id'),
                    (int) $documento->tipo_id
                );

                $firmaData = null;
                if ($firmarAhora && $profesionalObj && $profesionalObj->firma_nombre) {
                    $firmaData = $this->buildFirmaData($profesionalObj);
                }

                $pdfContent = Pdf::loadView('pdf.informe', [
                    'tipo'         => $tipoInforme->nombre ?? $tipoInforme->name,
                    'fecha'        => $fechaFormateada,
                    'paciente'     => $paciente->apellido . ', ' . $paciente->nombre,
                    'pacienteDNI'  => $paciente->dni,
                    'informe'      => $request->input('redaccion_informe'),
                    'diagnostico'  => $request->input('diagnostico'),
                    'codigo_cie10' => $request->input('codigo_cie10'),
                    'profesional'  => $profesionalObj?->name,
                    'firmaData'    => $firmaData,
                ])->output();

                $filename  = Str::random(20) . '.pdf';
                $directory = 'uploads/' . $request->input('paciente_id') . '/' . $request->input('tipo_id');
                Storage::disk('public')->put($directory . '/' . $filename, $pdfContent);
                $documento->document_files = json_encode([$filename]);

                if ($firmarAhora && $firmaData) {
                    $documento->firmado    = true;
                    $documento->firmado_por = $profesionalObj->id;
                    $documento->firmado_at  = now();
                }
            } else {
                $documento->redaccion = null;

                if ($request->hasFile('document_file')) {
                    $fileNames = [];
                    foreach ($request->file('document_file') as $file) {
                        if ($file->getClientOriginalExtension() === 'pdf') {
                            $directory = 'uploads/' . $request->input('paciente_id') . '/' . $request->input('tipo_id');
                            $filename  = Str::random(20) . '.pdf';
                            $file->storeAs($directory, $filename, 'public');
                            $fileNames[] = $filename;
                        }
                    }
                    $documento->document_files = json_encode($fileNames);
                }
            }

            $documento->save();

            // Recetas
            if ($request->hasFile('receta_file')) {
                foreach ($request->file('receta_file') as $recetaFile) {
                    $recetaDir  = 'recetas/' . $documento->id;
                    $recetaName = Str::random(20) . '.' . $recetaFile->getClientOriginalExtension();
                    $recetaFile->storeAs($recetaDir, $recetaName, 'public');

                    Receta::create([
                        'informe_id'     => $documento->id,
                        'archivo'        => $recetaDir . '/' . $recetaName,
                        'nombre_original'=> $recetaFile->getClientOriginalName(),
                        'tipo_mime'      => $recetaFile->getMimeType(),
                    ]);
                }
            }

            // Auditoría de firma
            if ($documento->firmado) {
                FirmaAuditoria::create([
                    'informe_id'        => $documento->id,
                    'user_id'           => $profesionalObj->id,
                    'firmado_at'        => $documento->firmado_at,
                    'ip_address'        => $request->ip(),
                    'version_documento' => 1,
                ]);
            }

            notify()->success('Informe cargado correctamente.', 'Nuevo informe');

            // Notifications
            $pacNombre  = $documento->paciente->apellido . ', ' . $documento->paciente->nombre;
            $tipoNombre = $documento->tipo->name ?? $documento->tipo->nombre ?? 'informe';
            $actor      = auth()->id();

            NotificacionService::informeCreado(auth()->user()->name, $pacNombre, $tipoNombre, $documento->id, $actor);

            $recetasSubidas = $request->hasFile('receta_file') ? count($request->file('receta_file')) : 0;
            if ($recetasSubidas > 0) {
                NotificacionService::recetaSubida(auth()->user()->name, $pacNombre, $documento->id, $documento->paciente_id, $recetasSubidas, $actor);
            }

            if ($request->filled('from_paciente')) {
                return redirect()->route('panel.paciente.show', $request->from_paciente)
                    ->with('success', 'Informe cargado exitosamente.');
            }

            return redirect()->route('panel.informe.index')->with('success', 'Informe creado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('informe_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informe = Informe::with(['paciente', 'tipo', 'profesional', 'firmadoPor', 'recetas', 'agenda'])
            ->findOrFail($id);

        $attachedFiles = $Informe->document_files
            ? json_decode($Informe->document_files, true)
            : [];

        return view('panel.informes.show', compact('Informe', 'attachedFiles'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informe = Informe::with(['recetas'])->findOrFail($id);

        abort_if(
            !auth()->user()->ownsOrAdmin($Informe, 'profesional_id'),
            Response::HTTP_FORBIDDEN,
            'Solo podés modificar informes que hayas cargado vos.'
        );

        if ($Informe->firmado) {
            return redirect()->route('panel.informe.show', $Informe->id)
                ->with('error', 'Este informe está firmado y no puede modificarse. Si necesitás hacer cambios, creá una nueva versión.');
        }

        $tipos = InformeTipo::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $pacientesDatos = Paciente::where('status', 1)->orderBy('apellido')->get();
        $pacientes = $pacientesDatos->mapWithKeys(function ($paciente) {
            return [$paciente->id => $paciente->apellido . ', ' . $paciente->nombre];
        })->prepend(trans('global.pleaseSelect'), '');

        $profesionales = User::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $Informe->load('tipo', 'paciente', 'profesional', 'recetas');

        $agendas = Agenda::where('paciente_id', $Informe->paciente_id)
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get(['id', 'fecha_hora_inicio', 'motivo']);

        $attachedFiles = json_decode($Informe->document_files, true);

        return view('panel.informes.edit', compact(
            'Informe', 'tipos', 'pacientes', 'profesionales', 'agendas', 'attachedFiles'
        ));
    }

    public function update(UpdateInformeRequest $request, $id)
    {
        try {
            $request->validate([
                'document_file.*' => 'nullable|mimes:pdf|max:10240',
                'receta_file.*'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            ]);

            abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $informe = Informe::with(['recetas'])->findOrFail($id);

            abort_if(
                !auth()->user()->ownsOrAdmin($informe, 'profesional_id'),
                Response::HTTP_FORBIDDEN,
                'Solo podés modificar informes que hayas cargado vos.'
            );

            abort_if($informe->firmado, 403, 'Este informe está firmado y no puede modificarse.');

            $attributes = [
                'paciente_id'    => $request->input('paciente_id'),
                'tipo_id'        => $request->input('tipo_id'),
                'profesional_id' => $request->input('profesional_id') ?: null,
                'agenda_id'      => $request->input('agenda_id') ?: null,
                'fecha'          => $request->input('fecha'),
                'diagnostico'    => $request->input('diagnostico'),
                'codigo_cie10'   => $request->input('codigo_cie10'),
            ];

            $tipoSeleccion = $request->input('tipo_seleccion');
            $firmarAhora   = $request->input('firmar_ahora') === '1';

            if ($tipoSeleccion === 'redaccion') {
                $oldFiles = json_decode($informe->document_files, true);
                if ($oldFiles) {
                    foreach ($oldFiles as $oldFile) {
                        Storage::disk('public')->delete(
                            'uploads/' . $informe->paciente_id . '/' . $informe->tipo_id . '/' . $oldFile
                        );
                    }
                }

                $pacienteObj    = Paciente::findOrFail($request->input('paciente_id'));
                $tipoInformeObj = InformeTipo::findOrFail($request->input('tipo_id'));
                $profesionalObj = $request->input('profesional_id')
                    ? User::find($request->input('profesional_id'))
                    : null;

                Carbon::setLocale('es');
                $fechaFormateada = Carbon::parse($request->input('fecha'))->translatedFormat('d \d\e F \d\e Y');

                $firmaData = null;
                if ($firmarAhora && $profesionalObj && $profesionalObj->firma_nombre) {
                    $firmaData = $this->buildFirmaData($profesionalObj);
                }

                $pdfContent = Pdf::loadView('pdf.informe', [
                    'tipo'         => $tipoInformeObj->nombre ?? $tipoInformeObj->name,
                    'fecha'        => $fechaFormateada,
                    'paciente'     => $pacienteObj->apellido . ', ' . $pacienteObj->nombre,
                    'pacienteDNI'  => $pacienteObj->dni,
                    'informe'      => $request->input('redaccion_informe'),
                    'diagnostico'  => $request->input('diagnostico'),
                    'codigo_cie10' => $request->input('codigo_cie10'),
                    'profesional'  => $profesionalObj?->name,
                    'firmaData'    => $firmaData,
                ])->output();

                $filename  = Str::random(20) . '.pdf';
                $directory = 'uploads/' . $request->input('paciente_id') . '/' . $request->input('tipo_id');
                Storage::disk('public')->put($directory . '/' . $filename, $pdfContent);

                $attributes['redaccion']      = $request->input('redaccion_informe');
                $attributes['document_files'] = json_encode([$filename]);
                $attributes['plantilla_documento_version_id'] = $this->resolverPlantillaVersionId(
                    $request->input('plantilla_documento_version_id'),
                    (int) $request->input('tipo_id')
                );

                if ($firmarAhora && $firmaData) {
                    $now = now();
                    $attributes['firmado']     = true;
                    $attributes['firmado_por'] = $profesionalObj->id;
                    $attributes['firmado_at']  = $now;
                }
            } else {
                $attributes['redaccion'] = null;

                if ($request->hasFile('document_file')) {
                    $oldFiles = json_decode($informe->document_files, true);
                    if ($oldFiles) {
                        foreach ($oldFiles as $oldFile) {
                            Storage::disk('public')->delete(
                                'uploads/' . $informe->paciente_id . '/' . $informe->tipo_id . '/' . $oldFile
                            );
                        }
                    }

                    $fileNames = [];
                    foreach ($request->file('document_file') as $file) {
                        if ($file->getClientOriginalExtension() === 'pdf') {
                            $filename = Str::random(20) . '.pdf';
                            $file->storeAs(
                                'uploads/' . $informe->paciente_id . '/' . $informe->tipo_id,
                                $filename, 'public'
                            );
                            $fileNames[] = $filename;
                        }
                    }
                    $attributes['document_files'] = json_encode($fileNames);
                }
            }

            $informe->updateWithLock($attributes, (int) $request->input('lock_version', 0));

            // Nuevas recetas
            if ($request->hasFile('receta_file')) {
                foreach ($request->file('receta_file') as $recetaFile) {
                    $recetaDir  = 'recetas/' . $informe->id;
                    $recetaName = Str::random(20) . '.' . $recetaFile->getClientOriginalExtension();
                    $recetaFile->storeAs($recetaDir, $recetaName, 'public');

                    Receta::create([
                        'informe_id'     => $informe->id,
                        'archivo'        => $recetaDir . '/' . $recetaName,
                        'nombre_original'=> $recetaFile->getClientOriginalName(),
                        'tipo_mime'      => $recetaFile->getMimeType(),
                    ]);
                }
            }

            // Auditoría si se firmó ahora
            if (!empty($attributes['firmado'])) {
                FirmaAuditoria::create([
                    'informe_id'        => $informe->id,
                    'user_id'           => $attributes['firmado_por'],
                    'firmado_at'        => $attributes['firmado_at'],
                    'ip_address'        => $request->ip(),
                    'version_documento' => 1,
                ]);
            }

            notify()->success('Informe actualizado correctamente.', 'Informe');

            if ($request->filled('from_paciente')) {
                return redirect()->route('panel.paciente.show', $request->from_paciente)
                    ->with('success', 'Informe actualizado exitosamente.');
            }

            return redirect()->route('panel.informe.show', $informe->id)
                ->with('success', 'Informe actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function firmar(Informe $informe, Request $request)
    {
        abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($informe->firmado, 422, 'Este informe ya está firmado.');
        abort_if(
            !auth()->user()->ownsOrAdmin($informe, 'profesional_id'),
            Response::HTTP_FORBIDDEN,
            'Solo podés firmar informes propios.'
        );
        abort_if(
            empty($informe->redaccion),
            422,
            'Solo se pueden firmar digitalmente los informes redactados en el sistema.'
        );

        $user = auth()->user();
        abort_if(
            !$user->firma_nombre,
            302,
            'Debés configurar tu firma digital en el perfil antes de firmar.'
        );

        if (!$user->firma_nombre) {
            return redirect()->route('panel.profile.index')
                ->with('error', 'Configurá tu firma digital en el perfil antes de firmar documentos.');
        }

        $paciente    = $informe->paciente;
        $tipoInforme = $informe->tipo;
        $firmaData   = $this->buildFirmaData($user);

        Carbon::setLocale('es');
        $fechaFormateada = Carbon::parse($informe->fecha)->translatedFormat('d \d\e F \d\e Y');

        $pdfContent = Pdf::loadView('pdf.informe', [
            'tipo'         => $tipoInforme->nombre ?? $tipoInforme->name,
            'fecha'        => $fechaFormateada,
            'paciente'     => $paciente->apellido . ', ' . $paciente->nombre,
            'pacienteDNI'  => $paciente->dni,
            'informe'      => $informe->redaccion,
            'diagnostico'  => $informe->diagnostico,
            'codigo_cie10' => $informe->codigo_cie10,
            'profesional'  => $user->name,
            'firmaData'    => $firmaData,
        ])->output();

        // Reemplazar PDF existente
        $oldFiles  = json_decode($informe->document_files, true) ?? [];
        $directory = 'uploads/' . $informe->paciente_id . '/' . $informe->tipo_id;
        foreach ($oldFiles as $oldFile) {
            Storage::disk('public')->delete($directory . '/' . $oldFile);
        }

        $filename = Str::random(20) . '.pdf';
        Storage::disk('public')->put($directory . '/' . $filename, $pdfContent);

        $now = now();
        $informe->update([
            'document_files' => json_encode([$filename]),
            'firmado'        => true,
            'firmado_por'    => $user->id,
            'firmado_at'     => $now,
        ]);

        FirmaAuditoria::create([
            'informe_id'        => $informe->id,
            'user_id'           => $user->id,
            'firmado_at'        => $now,
            'ip_address'        => $request->ip(),
            'version_documento' => count($oldFiles) + 1,
        ]);

        $pacNombre  = $informe->paciente->apellido . ', ' . $informe->paciente->nombre;
        $tipoNombre = $informe->tipo->name ?? $informe->tipo->nombre ?? 'informe';
        NotificacionService::informeFirmado($user->name, $pacNombre, $tipoNombre, $informe->id, $informe->paciente_id, $user->id);

        return redirect()->route('panel.informe.show', $informe->id)
            ->with('message', 'Informe firmado digitalmente y PDF generado.');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('informe_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informe = Informe::with(['recetas'])->findOrFail($id);

        abort_if(
            !auth()->user()->ownsOrAdmin($Informe, 'profesional_id'),
            Response::HTTP_FORBIDDEN,
            'Solo podés eliminar informes que hayas cargado vos.'
        );

        if ($Informe->document_files) {
            foreach (json_decode($Informe->document_files, true) as $fileName) {
                Storage::disk('public')->delete(
                    'uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $fileName
                );
            }
        }

        foreach ($Informe->recetas as $receta) {
            Storage::disk('public')->delete($receta->archivo);
            $receta->delete();
        }

        $Informe->delete();

        return back();
    }

    public function massDestroy(MassDestroyInformeRequest $request)
    {
        $Informes = Informe::with(['recetas'])->find(request('ids'));

        foreach ($Informes as $Informe) {
            if ($Informe->document_files) {
                foreach (json_decode($Informe->document_files, true) as $fileName) {
                    Storage::disk('public')->delete(
                        'uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $fileName
                    );
                }
            }
            foreach ($Informe->recetas as $receta) {
                Storage::disk('public')->delete($receta->archivo);
                $receta->delete();
            }
            $Informe->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    private function buildFirmaData(User $user): ?array
    {
        if (!$user->firma_nombre) {
            return null;
        }

        $base64 = null;
        if ($user->firma_imagen && Storage::disk('public')->exists($user->firma_imagen)) {
            $base64 = base64_encode(Storage::disk('public')->get($user->firma_imagen));
        }

        return [
            'base64'      => $base64,
            'nombre'      => $user->firma_nombre,
            'dni'         => $user->firma_dni,
            'matricula'   => $user->firma_matricula,
            'especialidad'=> $user->firma_especialidad_texto,
        ];
    }
}
