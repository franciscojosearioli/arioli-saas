<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicacion;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PlantillaImpresionController extends Controller
{
    public function historiaClinica()
    {
        return view('admin.impresion.historia-clinica');
    }

    public function fichaPaciente()
    {
        return view('admin.impresion.ficha-paciente');
    }
}