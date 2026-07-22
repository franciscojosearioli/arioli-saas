<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoConsentimiento;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TipoConsentimientoController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos = TipoConsentimiento::orderBy('nombre')->get();

        return view('admin.tipos-consentimiento.index', compact('tipos'));
    }

    public function create()
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tipos-consentimiento.form', ['tipo' => new TipoConsentimiento()]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'nombre'  => 'required|string|max:255',
            'paginas' => 'required|array|min:1',
        ]);

        TipoConsentimiento::create([
            'nombre'                     => $request->nombre,
            'descripcion'                => $request->descripcion,
            'contenido_paginas'          => $this->filtrarPaginas($request->input('paginas', [])),
            'requiere_firma_profesional' => (bool)($request->requiere_firma_profesional ?? false),
            'activo'                     => (bool)($request->activo ?? true),
        ]);

        return redirect()->route('admin.tipos-consentimiento.index')
            ->with('message', 'Tipo de consentimiento creado correctamente.');
    }

    public function edit(TipoConsentimiento $tipos_consentimiento)
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.tipos-consentimiento.form', ['tipo' => $tipos_consentimiento]);
    }

    public function update(Request $request, TipoConsentimiento $tipos_consentimiento)
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'nombre'  => 'required|string|max:255',
            'paginas' => 'required|array|min:1',
        ]);

        $tipos_consentimiento->update([
            'nombre'                     => $request->nombre,
            'descripcion'                => $request->descripcion,
            'contenido_paginas'          => $this->filtrarPaginas($request->input('paginas', [])),
            'requiere_firma_profesional' => (bool)($request->requiere_firma_profesional ?? false),
            'activo'                     => (bool)($request->activo ?? false),
        ]);

        return redirect()->route('admin.tipos-consentimiento.index')
            ->with('message', 'Tipo de consentimiento actualizado.');
    }

    private function filtrarPaginas(array $paginas): array
    {
        $result = array_values(array_filter($paginas, fn($p) => trim(strip_tags($p)) !== ''));
        return empty($result) ? [''] : $result;
    }

    public function destroy(TipoConsentimiento $tipos_consentimiento)
    {
        abort_unless(auth()->user()->roles->pluck('id')->intersect([1, 3])->isNotEmpty(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tipos_consentimiento->delete();

        return back()->with('message', 'Tipo eliminado.');
    }
}
