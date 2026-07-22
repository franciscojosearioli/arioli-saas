<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Página mínima real — no el odontograma todavía. Existe para que el
 * ítem de menú que aporta el Componente "odontologia" (navegacionSeed)
 * tenga un destino real, no un link roto. Ver docs/ARQUITECTURA_MODULAR.md,
 * Etapa 4.1.
 */
class OdontologiaController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('odontologia_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('panel.odontologia.index');
    }
}
