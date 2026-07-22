<?php 
namespace App\Http\Controllers\Panel;

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

        // Informes con archivos adjuntos, agrupados por tipo y ordenados por fecha
        $informes = Informe::with('tipo')
            ->where('paciente_id', $paciente->id)
            ->whereNotNull('document_files')
            ->orderBy('tipo_id')
            ->orderBy('fecha')
            ->orderBy('created_at')
            ->get()
            ->filter(fn($i) => !empty(json_decode($i->document_files, true)));

        // 1. Generar páginas de historia clínica base
        $pdfContent  = PDF::loadView('pdf.historia_clinica', compact('paciente'))->output();
        $tempHcPath  = storage_path('app/public/temp_hc_' . uniqid() . '.pdf');
        file_put_contents($tempHcPath, $pdfContent);

        $tempFiles = [$tempHcPath];

        try {
            $pdf       = new Fpdi();
            $pageCount = $pdf->setSourceFile($tempHcPath);
            for ($p = 1; $p <= $pageCount; $p++) {
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($p));
            }

            // 2. Por cada tipo de informe: carátula + documentos ordenados por fecha
            foreach ($informes->groupBy('tipo_id') as $informesDelTipo) {
                $tipoNombre = $informesDelTipo->first()->tipo?->name ?? 'Informe';
                $fechas     = $informesDelTipo->pluck('fecha')->filter()->sort();

                $fechaDesde = $fechas->first()
                    ? \Carbon\Carbon::parse($fechas->first())->format('d/m/Y')
                    : null;
                $fechaHasta = $fechas->last()
                    ? \Carbon\Carbon::parse($fechas->last())->format('d/m/Y')
                    : null;

                // Generar carátula con DomPDF para mantener diseño consistente
                $coverContent  = PDF::loadView('pdf.informe_caratula', [
                    'tipoNombre' => $tipoNombre,
                    'paciente'   => $paciente,
                    'count'      => $informesDelTipo->count(),
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                ])->output();

                $tempCoverPath = storage_path('app/public/temp_cover_' . uniqid() . '.pdf');
                file_put_contents($tempCoverPath, $coverContent);
                $tempFiles[] = $tempCoverPath;

                $coverPages = $pdf->setSourceFile($tempCoverPath);
                for ($p = 1; $p <= $coverPages; $p++) {
                    $pdf->AddPage();
                    $pdf->useTemplate($pdf->importPage($p));
                }

                // Adjuntar informes del tipo ordenados por fecha
                foreach ($informesDelTipo as $informe) {
                    $files = json_decode($informe->document_files, true);
                    if (empty($files)) continue;

                    foreach ($files as $file) {
                        $path = storage_path(
                            'app/public/uploads/' . $paciente->id . '/' . $informe->tipo_id . '/' . $file
                        );
                        if (!file_exists($path)) continue;

                        try {
                            $pc = $pdf->setSourceFile($path);
                            for ($p = 1; $p <= $pc; $p++) {
                                $pdf->AddPage();
                                $pdf->useTemplate($pdf->importPage($p));
                            }
                        } catch (\Exception $e) {
                            // Archivo corrupto o formato incompatible — se omite
                        }
                    }
                }
            }

            $outputPath = storage_path('app/public/combined_hc_' . uniqid() . '.pdf');
            $pdf->Output('F', $outputPath);

        } finally {
            foreach ($tempFiles as $f) {
                if (file_exists($f)) @unlink($f);
            }
        }

        return response()->file($outputPath)->deleteFileAfterSend(true);
    }   
    
    public function consentimientoPaciente($id)
    {
        $paciente = Paciente::with([
            'ficha_admision', 'padres_tutores', 'conyugue', 'hijos', 'hermanos',
            'educacion', 'laboral', 'historial_tratamientos', 'problematica', 'datos_adicionales',
        ])->findOrFail($id);

        // If already signed serve the pre-generated PDF directly
        $signedPath = 'consentimientos/' . $paciente->id . '/consentimiento_firmado.pdf';
        if ($paciente->consentimiento_firmado && \Illuminate\Support\Facades\Storage::disk('public')->exists($signedPath)) {
            return response()->file(
                \Illuminate\Support\Facades\Storage::disk('public')->path($signedPath),
                ['Content-Disposition' => 'inline; filename="consentimiento_firmado.pdf"']
            );
        }

        $pdf = PDF::loadView('pdf.consentimientoPaciente', [
            'paciente'    => $paciente,
            'firmaBase64' => null,
            'firmadoAt'   => null,
        ]);

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

    // Obtener los informes y ordenarlos por tipo y fecha
    $informes = Informe::with('tipo')
        ->where('paciente_id', $paciente->id)
        ->orderBy('tipo_id')
        ->orderBy('created_at')
        ->get();

    // Generar el PDF desde la vista HTML
    $pdfContent = PDF::loadView('pdf.ficha_historia_clinica', compact('paciente', 'informes'))->output();
    
    // Guardar el PDF temporalmente
$tempPdfPath = storage_path('app/public/temp_ficha_historia_clinica_' . uniqid() . '.pdf');
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

    // Agrupar informes por tipo_id
    $informesPorTipo = $informes->groupBy('tipo_id');

    // Procesar informes por tipo
    foreach ($informesPorTipo as $tipo_id => $informesDelTipo) {
        if (in_array($tipo_id, [1, 2, 3])) {
            // Agregar una página previa con el nombre del tipo solo una vez
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 50); // Fuente Arial y tamaño de letra grande
            $pdf->SetXY(0, 100);
            $pdf->MultiCell(0, 30, "Informe\n" . $informesDelTipo->first()->tipo->name, 0, 'C');
        }

        // Procesar cada informe del tipo
        foreach ($informesDelTipo as $informe) {
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
    }

    // Guardar el PDF combinado
    $outputPath = storage_path('app/public/combined_ficha_historia_clinica_' . uniqid() . '.pdf');
    $pdf->Output('F', $outputPath);

    // Eliminar el archivo PDF temporal
if (file_exists($tempPdfPath)) {
    unlink($tempPdfPath);
}

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

    public function medicacionPaciente($id, Request $request)
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

    // Revisar si se pasa fecha por request
    $fecha = $request->input('fecha');

    if (!$fecha) {
        // Obtener la última fecha de medicación del paciente
        $fecha = Medicacion::where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->value('fecha');
    }

    // Obtener las medicaciones de la fecha
    $medicaciones = Medicacion::where('paciente_id', $paciente->id)
        ->where('fecha', $fecha)
        ->get();

    $pdf = PDF::loadView('pdf.medicacion_paciente', compact('paciente', 'medicaciones', 'fecha'));

    return $pdf->stream('medicacion_paciente.pdf');
}

    public function esquemaMedicacion()
    {
            // Obtener los pacientes activos
    $pacientes = Paciente::whereHas('ficha_admision', function($query) {
        $query->whereNull('fecha_egreso');
    })->get();

    $medicaciones = collect();

    foreach ($pacientes as $paciente) {
        // Obtener la última fecha de medicación de cada paciente
        $ultimaFecha = Medicacion::where('paciente_id', $paciente->id)
            ->latest('fecha') // suponiendo que tengas campo 'fecha'
            ->value('fecha');

        if ($ultimaFecha) {
            // Traer todas las medicaciones de esa última fecha
            $meds = Medicacion::where('paciente_id', $paciente->id)
                ->where('fecha', $ultimaFecha)
                ->get();

            $medicaciones = $medicaciones->merge($meds);
        }
    }

    // Pasamos al PDF la colección de medicaciones filtradas
    $pdf = PDF::loadView('pdf.esquema_medicacion', compact('medicaciones'));

    return $pdf->stream('esquema_medicacion.pdf');
    }


}