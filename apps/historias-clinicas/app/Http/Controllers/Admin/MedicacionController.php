<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Medicacion\MassDestroyMedicacionRequest;
use App\Http\Requests\Medicacion\StoreMedicacionRequest;
use App\Http\Requests\Medicacion\UpdateMedicacionRequest;
use App\Models\Medicacion;
use App\Models\Paciente;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicacionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('medicacion_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Medicaciones = Medicacion::all();

        return view('admin.medicacion.index', compact('Medicaciones'));
    }

    public function create()
    {
        abort_if(Gate::denies('medicacion_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pacientes = Paciente::pluck('nombre', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.medicacion.create', compact('pacientes'));
    }

    public function store(StoreMedicacionRequest $request)
    {
        // Obtener los datos del formulario
        $data = $request->all();

        // Procesar cada conjunto de datos para medicamento
        foreach ($data['medicamento'] as $key => $medicamento) {
            // Crear un nuevo registro para cada fila
            Medicacion::create([
                'paciente_id' => $data['paciente_id'],
                'medicamento' => $medicamento,
                'cantidad' => $data['cantidad'][$key],
                'unidad' => $data['unidad'][$key],
                'horario' => $data['horario'][$key],
            ]);
        }
        return redirect()->route('admin.medicacion.index');
    }
    
    public function edit($id)
    {
        abort_if(Gate::denies('medicacion_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pacientes = Paciente::pluck('nombre', 'id')->prepend(trans('global.pleaseSelect'), '');

        $Medicacion = Medicacion::find($id);
    
        return view('admin.medicacion.edit', compact('pacientes', 'Medicacion'));
    }

    public function update(UpdateMedicacionRequest $request, $id)
    {

        $Medicacion = Medicacion::find($id);
        
        $Medicacion->update($request->all());

        return redirect()->route('admin.medicacion.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('medicacion_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Medicacion = Medicacion::find($id);

        return view('admin.medicacion.show', compact('Medicacion'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('medicacion_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $Medicacion = Medicacion::find($id);

        $Medicacion->delete();

        return back();
    }

    public function massDestroy(MassDestroyMedicacionRequest $request)
    {
        $Medicaciones = Medicacion::find(request('ids'));

        foreach ($Medicaciones as $Medicacion) {
            $Medicacion->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}



