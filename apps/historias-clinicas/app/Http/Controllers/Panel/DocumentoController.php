<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Informe;

class DocumentoController extends Controller
{
    public function show(Informe $informe)
    {
        $files = json_decode($informe->document_files) ?? [];

        return view('panel.documentos.show', compact('informe', 'files'));
    }
}
