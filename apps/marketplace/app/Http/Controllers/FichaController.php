<?php

namespace App\Http\Controllers;

use App\Models\FichaPropiedad;
use Illuminate\View\View;

class FichaController extends Controller
{
    public function show(FichaPropiedad $ficha): View
    {
        return view('fichas.show', ['ficha' => $ficha]);
    }
}
