<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\MassDestroyPacienteRequest;
use App\Http\Requests\Paciente\StorePacienteRequest;
use App\Http\Requests\Paciente\UpdatePacienteRequest;
use App\Models\Paciente;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PacienteController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('paciente_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Obtener todos los pacientes con sus relaciones cargadas
        $Pacientes = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->get();

        return view('admin.pacientes.index', compact('Pacientes'));
    }

    public function create()
    {
        abort_if(Gate::denies('paciente_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.pacientes.create');
    }

    public function store(StorePacienteRequest $request)
    {    
            // Guardar datos en la tabla 'pacientes'
            $paciente = Paciente::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'dni' => $request->dni,
                'fecha_nac' => $request->fecha_nac,
                'edad' => $request->edad,
                'sexo' => $request->sexo,
                'estado_civil' => $request->estado_civil,
                'obra_social' => $request->obra_social,
                'n_afiliado' => $request->n_afiliado,
                'provincia' => $request->provincia,
                'localidad' => $request->localidad,
                'calle' => $request->calle,
                'calle_numero' => $request->calle_numero,
                'calle_piso' => $request->calle_piso,
                'calle_dpto' => $request->calle_dpto,
                'familiar_hijos' => $request->familiar_hijos,
                'familiar_hermanos' => $request->familiar_hermanos,
                'historial_tratamiento' => $request->historial_tratamiento,
                // Otros campos de 'pacientes' según sea necesario
            ]);

            // Guardar datos en la tabla 'pacientes_ficha_admision'
            $paciente->ficha_admision()->create([
                'fecha_ingreso' => $request->fecha_ingreso,
                'modalidad' => $request->modalidad,
                'fecha_egreso' => $request->fecha_egreso,
                'tipo_egreso' => $request->tipo_egreso,
                // Otros campos de 'pacientes_ficha_admision' según sea necesario
            ]);
            
            // Guardar datos en la tabla 'pacientes_padres_tutor'
            $paciente->padres_tutores()->create([
                'padre_nombre' => $request->padre_nombre,
                'padre_apellido' => $request->padre_apellido,
                'padre_responsable' => $request->padre_responsable,
                'madre_nombre' => $request->madre_nombre,
                'madre_apellido' => $request->madre_apellido,
                'madre_responsable' => $request->madre_responsable,
                'tutor_nombre' => $request->tutor_nombre,
                'tutor_apellido' => $request->tutor_apellido,
                'tutor_responsable' => $request->tutor_responsable,
                // Otros campos de 'pacientes_padres_tutor' según sea necesario
            ]);

            // Guardar datos en la tabla 'pacientes_conyugues' asociada al paciente
            $paciente->conyugue()->create([
                'conyugue_nombre' => $request->conyugue_nombre,
                'conyugue_apellido' => $request->conyugue_apellido,
                'conyugue_edad' => $request->conyugue_edad,
                'conyugue_domicilio' => $request->conyugue_domicilio,
                // Otros campos de 'pacientes_conyugues' según sea necesario
            ]);    
            
            if($request->familiar_hijos != 'No'){
                // Verifica si hay datos de hermanos en la solicitud
                if ($request->has('hijos_nombre') && is_array($request->hijos_nombre)) {
                    $hijosNombres = $request->hijos_nombre;
                    $hijosEdades = $request->hijos_edad;
                    $hijosTutor = $request->hijos_tutor;

                    for ($i = 0; $i < count($hijosNombres); $i++) {
                        $paciente->hijos()->create([
                            'hijos_nombre' => $hijosNombres[$i],
                            'hijos_edad' => $hijosEdades[$i],
                            'hijos_tutor' => $hijosTutor[$i],
                        ]);
                    }
                }
            }

            if($request->familiar_hermanos != 'No'){
                // Verifica si hay datos de hermanos en la solicitud
                if ($request->has('hermanos_nombre') && is_array($request->hermanos_nombre)) {
                    $hermanosNombres = $request->hermanos_nombre;
                    $hermanosEdades = $request->hermanos_edad;
                    $hermanosConvive = $request->hermanos_convive;
            
                    for ($i = 0; $i < count($hermanosNombres); $i++) {
                        $paciente->hermanos()->create([
                            'hermanos_nombre' => $hermanosNombres[$i],
                            'hermanos_edad' => $hermanosEdades[$i],
                            'hermanos_convive' => $hermanosConvive[$i],
                        ]);
                    }
                }
            }
            // Guardar datos en la tabla 'pacientes_educacion' asociada al paciente
            $paciente->educacion()->create([
                'primaria_completa' => $request->primaria_completa,
                'primaria_incompleta' => $request->primaria_incompleta,
                'primaria_ultimo_anio' => $request->primaria_ultimo_anio,
                'primaria_expulsado' => $request->primaria_expulsado,
                'primaria_interrumpido' => $request->primaria_interrumpido,
                'primaria_cambios' => $request->primaria_cambios,
                'secundaria_completa' => $request->secundaria_completa,
                'secundaria_incompleta' => $request->secundaria_incompleta,
                'secundaria_ultimo_anio' => $request->secundaria_ultimo_anio,
                'secundaria_expulsado' => $request->secundaria_expulsado,
                'secundaria_interrumpido' => $request->secundaria_interrumpido,
                'secundaria_cambios' => $request->secundaria_cambios,
                'terciaria_completa' => $request->terciaria_completa,
                'terciaria_incompleta' => $request->terciaria_incompleta,
                'terciaria_ultimo_anio' => $request->terciaria_ultimo_anio,
                'terciaria_expulsado' => $request->terciaria_expulsado,
                'terciaria_interrumpido' => $request->terciaria_interrumpido,
                'terciaria_cambios' => $request->terciaria_cambios,
                'facultad_completa' => $request->facultad_completa,
                'facultad_incompleta' => $request->facultad_incompleta,
                'facultad_ultimo_anio' => $request->facultad_ultimo_anio,
                'facultad_expulsado' => $request->facultad_expulsado,
                'facultad_interrumpido' => $request->facultad_interrumpido,
                'facultad_cambios' => $request->facultad_cambios,
                'observaciones' => $request->observaciones,
                // Otros campos de 'pacientes_educacion' según sea necesario
            ]);

            // Guardar datos en la tabla 'pacientes_laboral' asociada al paciente
            $paciente->laboral()->create([
                'actividad_laboral' => $request->actividad_laboral,
                'empresa_laboral' => $request->empresa_laboral,
                'cargo_laboral' => $request->cargo_laboral,
                'antiguedad_laboral' => $request->antiguedad_laboral,
                'antecedente_laboral' => $request->antecedente_laboral,
                // Otros campos de 'pacientes_laboral' según sea necesario
            ]);

            if($request->historial_tratamiento != 'No'){
                // Verifica si hay datos de hermanos en la solicitud
                if ($request->has('lugar') && is_array($request->lugar)) {
                    $lugar = $request->lugar;
                    $duracion = $request->duracion;

                    for ($i = 0; $i < count($lugar); $i++) {
                        $paciente->historial_tratamientos()->create([
                            'lugar' => $lugar[$i],
                            'duracion' => $duracion[$i],
                        ]);
                    }
                }
            }

            // Guardar datos en la tabla 'pacientes_problematica' asociada al paciente
            $paciente->problematica()->create([
                'problematica' => $request->problematica,
                'problematica_detalles' => $request->problematica_detalles,
                // Otros campos de 'pacientes_problematica' según sea necesario
            ]);

            // Guardar datos en la tabla 'pacientes_datos_adicionales' asociada al paciente
            $paciente->datos_adicionales()->create([
                'abuso_sexual' => $request->abuso_sexual,
                'sobredosis' => $request->sobredosis,
                'antecedentes_legales' => $request->antecedentes_legales,
                'analfabeto' => $request->analfabeto,
                'padres_separados' => $request->padres_separados,
                'privado_libertad' => $request->privado_libertad,
                'tiempo_privado_libertad' => $request->tiempo_privado_libertad,
                'enfermedad_cronica' => $request->enfermedad_cronica,
                'enfermedad_cronica_detalles' => $request->enfermedad_cronica_detalles,
                'alergia' => $request->alergia,
                'alergia_detalles' => $request->alergia_detalles,
                // Otros campos de 'pacientes_datos_adicionales' según sea necesario
            ]);

           
        // Redirigir a la vista index de pacientes después de guardar
        return redirect()->route('admin.paciente.index');
    }

    public function edit(Paciente $Paciente)
    {
        abort_if(Gate::denies('paciente_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Cargar relaciones necesarias del paciente, según sea necesario en tu aplicación
        $Paciente->load([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ]);

        return view('admin.pacientes.edit', compact('Paciente'));
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        // Guardar datos en la tabla 'pacientes'
        $paciente->updateWithLock([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'dni' => $request->dni,
            'fecha_nac' => $request->fecha_nac,
            'edad' => $request->edad,
            'sexo' => $request->sexo,
            'estado_civil' => $request->estado_civil,
            'obra_social' => $request->obra_social,
            'n_afiliado' => $request->n_afiliado,
            'provincia' => $request->provincia,
            'localidad' => $request->localidad,
            'calle' => $request->calle,
            'calle_numero' => $request->calle_numero,
            'calle_piso' => $request->calle_piso,
            'calle_dpto' => $request->calle_dpto,
            'familiar_hijos' => $request->familiar_hijos,
            'familiar_hermanos' => $request->familiar_hermanos,
            'historial_tratamiento' => $request->historial_tratamiento,
        ], (int) $request->input('lock_version', 0));

        // Guardar datos en la tabla 'pacientes_ficha_admision'
        $paciente->ficha_admision()->update([
            'fecha_ingreso' => $request->fecha_ingreso,
            'modalidad' => $request->modalidad,
            'fecha_egreso' => $request->fecha_egreso,
            'tipo_egreso' => $request->tipo_egreso,
            // Otros campos de 'pacientes_ficha_admision' según sea necesario
        ]);
        
        // Guardar datos en la tabla 'pacientes_padres_tutor'
        $paciente->padres_tutores()->update([
            'padre_nombre' => $request->padre_nombre,
            'padre_apellido' => $request->padre_apellido,
            'padre_responsable' => $request->padre_responsable,
            'madre_nombre' => $request->madre_nombre,
            'madre_apellido' => $request->madre_apellido,
            'madre_responsable' => $request->madre_responsable,
            'tutor_nombre' => $request->tutor_nombre,
            'tutor_apellido' => $request->tutor_apellido,
            'tutor_responsable' => $request->tutor_responsable,
            // Otros campos de 'pacientes_padres_tutor' según sea necesario
        ]);

        // Guardar datos en la tabla 'pacientes_conyugues' asociada al paciente
        $paciente->conyugue()->update([
            'conyugue_nombre' => $request->conyugue_nombre,
            'conyugue_apellido' => $request->conyugue_apellido,
            'conyugue_edad' => $request->conyugue_edad,
            'conyugue_domicilio' => $request->conyugue_domicilio,
            // Otros campos de 'pacientes_conyugues' según sea necesario
        ]);    
        
        if($request->familiar_hijos != 'No'){
            // Verifica si hay datos de hermanos en la solicitud
            if ($request->has('hijos_nombre') && is_array($request->hijos_nombre)) {
                $hijosNombres = $request->hijos_nombre;
                $hijosEdades = $request->hijos_edad;
                $hijosTutor = $request->hijos_tutor;

                for ($i = 0; $i < count($hijosNombres); $i++) {
                    $paciente->hijos()->update([
                        'hijos_nombre' => $hijosNombres[$i],
                        'hijos_edad' => $hijosEdades[$i],
                        'hijos_tutor' => $hijosTutor[$i],
                    ]);
                }
            }
        }

        if($request->familiar_hermanos != 'No'){
            // Verifica si hay datos de hermanos en la solicitud
            if ($request->has('hermanos_nombre') && is_array($request->hermanos_nombre)) {
                $hermanosNombres = $request->hermanos_nombre;
                $hermanosEdades = $request->hermanos_edad;
                $hermanosConvive = $request->hermanos_convive;
        
                for ($i = 0; $i < count($hermanosNombres); $i++) {
                    $paciente->hermanos()->update([
                        'hermanos_nombre' => $hermanosNombres[$i],
                        'hermanos_edad' => $hermanosEdades[$i],
                        'hermanos_convive' => $hermanosConvive[$i],
                    ]);
                }
            }
        }
        // Guardar datos en la tabla 'pacientes_educacion' asociada al paciente
        $paciente->educacion()->update([
            'primaria_completa' => $request->primaria_completa,
            'primaria_incompleta' => $request->primaria_incompleta,
            'primaria_ultimo_anio' => $request->primaria_ultimo_anio,
            'primaria_expulsado' => $request->primaria_expulsado,
            'primaria_interrumpido' => $request->primaria_interrumpido,
            'primaria_cambios' => $request->primaria_cambios,
            'secundaria_completa' => $request->secundaria_completa,
            'secundaria_incompleta' => $request->secundaria_incompleta,
            'secundaria_ultimo_anio' => $request->secundaria_ultimo_anio,
            'secundaria_expulsado' => $request->secundaria_expulsado,
            'secundaria_interrumpido' => $request->secundaria_interrumpido,
            'secundaria_cambios' => $request->secundaria_cambios,
            'terciaria_completa' => $request->terciaria_completa,
            'terciaria_incompleta' => $request->terciaria_incompleta,
            'terciaria_ultimo_anio' => $request->terciaria_ultimo_anio,
            'terciaria_expulsado' => $request->terciaria_expulsado,
            'terciaria_interrumpido' => $request->terciaria_interrumpido,
            'terciaria_cambios' => $request->terciaria_cambios,
            'facultad_completa' => $request->facultad_completa,
            'facultad_incompleta' => $request->facultad_incompleta,
            'facultad_ultimo_anio' => $request->facultad_ultimo_anio,
            'facultad_expulsado' => $request->facultad_expulsado,
            'facultad_interrumpido' => $request->facultad_interrumpido,
            'facultad_cambios' => $request->facultad_cambios,
            'observaciones' => $request->observaciones,
            // Otros campos de 'pacientes_educacion' según sea necesario
        ]);

        // Guardar datos en la tabla 'pacientes_laboral' asociada al paciente
        $paciente->laboral()->update([
            'actividad_laboral' => $request->actividad_laboral,
            'empresa_laboral' => $request->empresa_laboral,
            'cargo_laboral' => $request->cargo_laboral,
            'antiguedad_laboral' => $request->antiguedad_laboral,
            'antecedente_laboral' => $request->antecedente_laboral,
            // Otros campos de 'pacientes_laboral' según sea necesario
        ]);

        if($request->historial_tratamiento != 'No'){
            // Verifica si hay datos de hermanos en la solicitud
            if ($request->has('lugar') && is_array($request->lugar)) {
                $lugar = $request->lugar;
                $duracion = $request->duracion;

                for ($i = 0; $i < count($lugar); $i++) {
                    $paciente->historial_tratamientos()->update([
                        'lugar' => $lugar[$i],
                        'duracion' => $duracion[$i],
                    ]);
                }
            }
        }

        // Guardar datos en la tabla 'pacientes_problematica' asociada al paciente
        $paciente->problematica()->update([
            'problematica' => $request->problematica,
            'problematica_detalles' => $request->problematica_detalles,
            // Otros campos de 'pacientes_problematica' según sea necesario
        ]);

        // Guardar datos en la tabla 'pacientes_datos_adicionales' asociada al paciente
        $paciente->datos_adicionales()->update([
            'abuso_sexual' => $request->abuso_sexual,
            'sobredosis' => $request->sobredosis,
            'antecedentes_legales' => $request->antecedentes_legales,
            'analfabeto' => $request->analfabeto,
            'padres_separados' => $request->padres_separados,
            'privado_libertad' => $request->privado_libertad,
            'tiempo_privado_libertad' => $request->tiempo_privado_libertad,
            'enfermedad_cronica' => $request->enfermedad_cronica,
            'enfermedad_cronica_detalles' => $request->enfermedad_cronica_detalles,
            'alergia' => $request->alergia,
            'alergia_detalles' => $request->alergia_detalles,
            // Otros campos de 'pacientes_datos_adicionales' según sea necesario
        ]);

        // Redirigir a la vista index de pacientes después de actualizar
        return redirect()->route('admin.paciente.index');
    }

    public function show(Paciente $Paciente)
    {
        abort_if(Gate::denies('paciente_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Cargar relaciones necesarias
        $Paciente->load([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ]);
        
        // Obtener informes del paciente
        $Informes = $Paciente->informes;

        // Obtener Esquemas del paciente
        $Medicaciones = $Paciente->medicacion;

        // Retornar vista mostrando los datos del paciente
        return view('admin.pacientes.show', compact('Paciente', 'Informes', 'Medicaciones'));
    }

    public function destroy(Paciente $paciente)
    {
        // Verificar permiso usando Gate
        abort_if(Gate::denies('paciente_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    
        // Eliminar registros relacionados
        $paciente->ficha_admision()->delete();
        $paciente->padres_tutores()->delete();
        $paciente->conyugue()->delete();
        $paciente->hijos()->delete();
        $paciente->hermanos()->delete();
        $paciente->educacion()->delete();
        $paciente->laboral()->delete();
        $paciente->historial_tratamientos()->delete();
        $paciente->problematica()->delete();
        $paciente->datos_adicionales()->delete();
    
        // Eliminar paciente principal
        $paciente->delete();
    
        // Redirigir de vuelta
        return back();
    }

    public function massDestroy(MassDestroyPacienteRequest $request)
    {
        $pacienteIds = $request->input('ids');
    
        // Eliminar cada paciente y sus registros relacionados
        foreach ($pacienteIds as $pacienteId) {
            $paciente = Paciente::findOrFail($pacienteId);
    
            // Eliminar registros relacionados
            $paciente->ficha_admision()->delete();
            $paciente->padres_tutores()->delete();
            $paciente->conyugue()->delete();
            $paciente->hijos()->delete();
            $paciente->hermanos()->delete();
            $paciente->educacion()->delete();
            $paciente->laboral()->delete();
            $paciente->historial_tratamientos()->delete();
            $paciente->problematica()->delete();
            $paciente->datos_adicionales()->delete();
    
            // Eliminar paciente principal
            $paciente->delete();
        }
    
        // Devolver respuesta sin contenido
        return response(null, Response::HTTP_NO_CONTENT);
    }

}