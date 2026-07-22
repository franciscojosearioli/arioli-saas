<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Informe;
use App\Models\Medicacion;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;
use Barryvdh\DomPDF\Facade\Pdf; // Importa la fachada de DomPDF

class PdfController extends Controller
{
    
    public function historiaClinica($id)
    {
        $paciente = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->findOrFail($id);
        $pdf = PDF::loadView('pdf.historia_clinica', compact('paciente'));

        return $pdf->stream('historia_clinica.pdf');
    }   

    public function consentimientoPaciente($id)
    {
        $paciente = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->findOrFail($id);
        $pdf = PDF::loadView('pdf.consentimientoPaciente', compact('paciente'));

        return $pdf->stream('consentimientoPaciente.pdf');
    }   

    public function fichaHistoriaClinica($id)
    {
        $paciente = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->findOrFail($id);

        $informes = Informe::with('tipo')->where('paciente_id', $paciente->id)->get();

        // Generar el PDF desde la vista HTML
        $pdfContent = PDF::loadView('pdf.ficha_historia_clinica', compact('paciente', 'informes'))->output();
        
        // Guardar el PDF temporalmente
        $tempPdfPath = storage_path('app/public/temp_ficha_historia_clinica.pdf');
        file_put_contents($tempPdfPath, $pdfContent);

        // Crear una instancia de FPDI
        $pdf = new Fpdi();
        
        // Agregar PDF generado desde HTML
        $pageCount = $pdf->setSourceFile($tempPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx);
        }

    // Agregar PDFs de los informes
    foreach ($informes as $informe) {
        if (in_array($informe->tipo_id, [1, 2, 3, 4, 5])) {
            // Agregar una página previa con el nombre del tipo
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 50); // Fuente Arial y tamaño de letra grande
            $pdf->SetXY(0, 100);
            $pdf->MultiCell(0, 30, "Informe\n" . $informe->tipo->name, 0, 'C');
        }

        $files = json_decode($informe->document_files); // Suponiendo que document_files es un JSON con una lista de archivos

        if (is_array($files)) {
            foreach ($files as $file) {
                $pdfPath = storage_path('app/public/uploads/' . $informe->paciente->id . '/' . $informe->tipo->id . '/' . $file);

                if (file_exists($pdfPath)) {
                    $pageCount = $pdf->setSourceFile($pdfPath);

                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $pdf->AddPage();
                        $tplIdx = $pdf->importPage($pageNo);
                        $pdf->useTemplate($tplIdx);
                    }
                }
            }
        }
    }

        // Guardar el PDF combinado
        $outputPath = storage_path('app/public/combined_ficha_historia_clinica.pdf');
        $pdf->Output('F', $outputPath);

        // Eliminar el archivo PDF temporal
        unlink($tempPdfPath);

        // Devolver el archivo combinado
        return response()->file($outputPath);
    }   

    public function fichaPaciente($id)
    {
        $paciente = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->findOrFail($id);
        $pdf = PDF::loadView('pdf.ficha_paciente', compact('paciente'));

        return $pdf->stream('ficha_paciente.pdf');
    }

    public function medicacionPaciente($id)
    {
        $paciente = Paciente::with([
            'ficha_admision',
            'padres_tutores',
            'conyugue',
            'hijos',
            'hermanos',
            'educacion',
            'laboral',
            'historial_tratamientos',
            'problematica',
            'datos_adicionales',
        ])->findOrFail($id);
        $medicaciones = Medicacion::where('paciente_id', $paciente->id)->get();
        $pdf = PDF::loadView('pdf.medicacion_paciente', compact('paciente', 'medicaciones'));

        return $pdf->stream('medicacion_paciente.pdf');
    }

    public function esquemaMedicacion()
    {
        $medicaciones =  Medicacion::whereHas('paciente', function($query) {
            $query->whereHas('ficha_admision', function($query) {
                $query->whereNull('fecha_egreso');
            });
        })->get();
        $pdf = PDF::loadView('pdf.esquema_medicacion', compact('medicaciones'));

        return $pdf->stream('esquema_medicacion.pdf');
    }


}