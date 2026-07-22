<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Informe\MassDestroyInformeRequest;
use App\Http\Requests\Informe\StoreInformeRequest;
use App\Http\Requests\Informe\UpdateInformeRequest;
use App\Models\Informe;
use App\Models\InformeTipo;
use App\Models\Paciente;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class InformeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('informe_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informes = Informe::with(['paciente', 'tipo'])->get();

        return view('admin.informes.index', compact('Informes'));
    }

    public function create()
    {
        abort_if(Gate::denies('informe_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos         = InformeTipo::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $pacientesDatos = Paciente::orderBy('apellido')->get();
        $pacientes     = $pacientesDatos->mapWithKeys(fn($p) => [$p->id => $p->apellido.', '.$p->nombre])
                            ->prepend(trans('global.pleaseSelect'), '');
        $profesionales = User::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.informes.create', compact('tipos', 'pacientes', 'profesionales'));
    }

    public function store(StoreInformeRequest $request)
    {
        try {
            // Crear una nueva instancia de Documento
            $documento = new Informe();

            // Asignar los valores del formulario al modelo Documento
            $documento->paciente_id    = $request->input('paciente_id');
            $documento->tipo_id        = $request->input('tipo_id');
            $documento->profesional_id = $request->input('profesional_id') ?: null;
            $documento->fecha          = $request->input('fecha');
            $documento->diagnostico    = $request->input('diagnostico');
            $documento->codigo_cie10   = $request->input('codigo_cie10');
            
            // Guardar el modelo Documento en la base de datos
            $documento->save();

            // Verificar y almacenar los archivos
            if ($request->hasFile('document_file')) {
                $files = $request->file('document_file');
                $fileNames = [];

                // Iterar sobre cada archivo cargado
                foreach ($files as $file) {
                    // Obtener el ID del paciente para crear el directorio
                    $pacienteId = $request->input('paciente_id');
                    $tipoInf = $request->input('tipo_id');
                    $directory = 'uploads/' . $pacienteId . '/'. $tipoInf;

                    // Generar un nombre único para el archivo
                    $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

                    // Almacenar el archivo en el sistema de archivos público
                    $path = $file->storeAs($directory, $filename, 'public');

                    // Guardar el nombre del archivo en el array $fileNames
                    $fileNames[] = $filename;
                }

                // Convertir el array $fileNames a formato JSON
                $documento->document_files = json_encode($fileNames);
                $documento->save(); // Guardar los nombres de los archivos en el modelo Documento
            }

            // Redirigir con un mensaje de éxito si todo fue exitoso
            return redirect()->route('admin.informe.index')
                ->with('success', 'Documento creado exitosamente.');

        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la creación del documento o carga de archivos
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos         = InformeTipo::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $pacientesDatos = Paciente::orderBy('apellido')->get();
        $pacientes     = $pacientesDatos->mapWithKeys(fn($p) => [$p->id => $p->apellido.', '.$p->nombre])
                            ->prepend(trans('global.pleaseSelect'), '');
        $profesionales = User::orderBy('name')->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $Informe = Informe::findOrFail($id);
        $Informe->load('tipo', 'paciente', 'profesional');

        $attachedFiles = json_decode($Informe->document_files, true);

        return view('admin.informes.edit', compact('Informe', 'tipos', 'pacientes', 'profesionales', 'attachedFiles'));
    }
    
    public function update(UpdateInformeRequest $request, $id)
    {
        try {
            abort_if(Gate::denies('informe_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $Informe = Informe::findOrFail($id);

            $attributes = [
                'paciente_id'    => $request->input('paciente_id'),
                'tipo_id'        => $request->input('tipo_id'),
                'profesional_id' => $request->input('profesional_id') ?: null,
                'fecha'          => $request->input('fecha'),
                'diagnostico'    => $request->input('diagnostico'),
                'codigo_cie10'   => $request->input('codigo_cie10'),
            ];

            if ($request->hasFile('document_file')) {
                $oldFiles = json_decode($Informe->document_files, true);
                if ($oldFiles) {
                    foreach ($oldFiles as $oldFile) {
                        Storage::disk('public')->delete('uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $oldFile);
                    }
                }

                $fileNames = [];
                foreach ($request->file('document_file') as $file) {
                    $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id, $filename, 'public');
                    $fileNames[] = $filename;
                }
                $attributes['document_files'] = json_encode($fileNames);
            }

            $Informe->updateWithLock($attributes, (int) $request->input('lock_version', 0));

            return redirect()->route('admin.informe.index')
                ->with('success', 'Informe actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('informe_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Obtener el informe basado en el ID proporcionado
        $Informe = Informe::findOrFail($id);

        // Decodificar los nombres de archivo adjuntos si existen
        $attachedFiles = [];
        if ($Informe->document_files) {
            $attachedFiles = json_decode($Informe->document_files, true);
        }

        return view('admin.informes.show', compact('Informe', 'attachedFiles'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('informe_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Informe = Informe::findOrFail($id);

        // Eliminar archivos adjuntos del servidor si existen
        if ($Informe->document_files) {
            $attachedFiles = json_decode($Informe->document_files, true);
            foreach ($attachedFiles as $fileName) {
                Storage::disk('public')->delete('uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $fileName);
            }
        }
    
        // Eliminar el informe de la base de datos
        $Informe->delete();

        return back();
    }

    public function massDestroy(MassDestroyInformeRequest $request)
    {
        $Informes = Informe::find(request('ids'));
    
        foreach ($Informes as $Informe) {
            // Eliminar archivos adjuntos del servidor si existen
            if ($Informe->document_files) {
                $attachedFiles = json_decode($Informe->document_files, true);
                foreach ($attachedFiles as $fileName) {
                    Storage::disk('public')->delete('uploads/' . $Informe->paciente_id . '/' . $Informe->tipo_id . '/' . $fileName);
                }
            }
    
            // Eliminar el informe de la base de datos
            $Informe->delete();
        }
    
        return response(null, Response::HTTP_NO_CONTENT);
    }
}



