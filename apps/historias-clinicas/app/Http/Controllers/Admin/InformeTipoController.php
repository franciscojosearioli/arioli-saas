<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Informe\MassDestroyInformeTipoRequest;
use App\Http\Requests\Informe\StoreInformeTipoRequest;
use App\Http\Requests\Informe\UpdateInformeTipoRequest;
use App\Models\InformeTipo;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InformeTipoController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('informe_tipo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $InformeTipos = InformeTipo::all();

        return view('admin.informes.tipos.index', compact('InformeTipos'));
    }

    public function create()
    {
        abort_if(Gate::denies('informe_tipo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.informes.tipos.create');
    }

    public function store(StoreInformeTipoRequest $request)
    {
        $InformeTipo = InformeTipo::create($request->all());

        return redirect()->route('admin.informes.tipos.index');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('informe_tipo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $InformeTipo = InformeTipo::find($id);

        return view('admin.informes.tipos.edit', compact('InformeTipo'));
    }

    public function update(UpdateInformeTipoRequest $request, $id)
    {
        $InformeTipo = InformeTipo::find($id);

        $InformeTipo->update($request->all());

        return redirect()->route('admin.informes.tipos.index');
    }

    public function show($id)
    {
        abort_if(Gate::denies('informe_tipo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $InformeTipo = InformeTipo::find($id);

        return view('admin.informes.tipos.show', compact('InformeTipo'));
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('informe_tipo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $InformeTipo = InformeTipo::find($id);

        $InformeTipo->delete();

        return back();
    }

    public function massDestroy(MassDestroyInformeTipoRequest $request)
    {
        $InformeTipos = InformeTipo::find(request('ids'));

        foreach ($InformeTipos as $InformeTipo) {
            $InformeTipo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}