<?php 

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait PacienteDocumentacionUploadingTrait
{
    public function storeMedia(Request $request)
    {
        try {
            // Obtener el proyecto_id del request
            $pacienteId = $request->input('paciente_id');
    
            // Validar si se envió un archivo
            if ($request->hasFile('file')) {
                $file = $request->file('file');
    
                // Aquí procesa y guarda el archivo según tu lógica
                // Por ejemplo:
                $name = uniqid() . '_' . trim($file->getClientOriginalName());
                $path = $file->storeAs("uploads/pacientes/{$pacienteId}", $name, 'public');
    
                return response()->json(['name' => $path], 200);
            } else {
                throw new \Exception('No se ha enviado ningún archivo.');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}