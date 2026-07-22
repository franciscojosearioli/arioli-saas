<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EspecialidadController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('especialidad_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $especialidades = Especialidad::withCount('profesionales')->get();

        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        abort_if(Gate::denies('especialidad_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.especialidades.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('especialidad_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Especialidad::create($request->only('nombre', 'descripcion'));

        notify()->success('Especialidad creada correctamente.', 'Nueva especialidad');

        return redirect()->route('admin.especialidades.index');
    }

    public function edit(Especialidad $especialidad)
    {
        abort_if(Gate::denies('especialidad_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        abort_if(Gate::denies('especialidad_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $especialidad->update($request->only('nombre', 'descripcion'));

        notify()->success('Especialidad actualizada correctamente.', 'Dato actualizado');

        return redirect()->route('admin.especialidades.index');
    }

    public function destroy(Especialidad $especialidad)
    {
        abort_if(Gate::denies('especialidad_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $especialidad->delete();

        return back();
    }
}
