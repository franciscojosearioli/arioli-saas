<?php

namespace App\Http\Controllers;

use App\Models\Desarrollo;
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
        // §08: "el de la constructora... con los Desarrollos a su cargo"
        // — bloqueado hasta que Desarrollo se sincronizara como entidad
        // real (ver el comentario que quedó en PerfilConstructora).
        $desarrollos = Desarrollo::where('tenant_id', $perfil->tenant_id)
            ->where('constructora_id', $perfil->constructora_id)
            ->orderBy('nombre')
            ->get();

        return view('perfiles.constructora', ['perfil' => $perfil, 'desarrollos' => $desarrollos]);
    }
}
