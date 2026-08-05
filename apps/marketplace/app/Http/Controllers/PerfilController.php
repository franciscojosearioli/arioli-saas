<?php

namespace App\Http\Controllers;

use App\Models\FichaPropiedad;
use App\Models\PerfilConstructora;
use App\Models\PerfilInmobiliaria;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function inmobiliaria(PerfilInmobiliaria $perfil): View
    {
        $fichas = FichaPropiedad::where('tenant_id', $perfil->tenant_id)
            ->orderByDesc('publicada_en')
            ->paginate(12);

        return view('perfiles.inmobiliaria', ['perfil' => $perfil, 'fichas' => $fichas]);
    }

    public function constructora(PerfilConstructora $perfil): View
    {
        return view('perfiles.constructora', ['perfil' => $perfil]);
    }
}
