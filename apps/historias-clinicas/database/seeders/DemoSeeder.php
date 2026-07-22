<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Carbon::setLocale('es');

        // ════════════════════════════════════════════
        //  CONFIGURACIÓN DEL SISTEMA
        // ════════════════════════════════════════════
        $configExists = DB::select("SELECT COUNT(*) as n FROM configuracion_sistema")[0]->n ?? 0;
        if ($configExists) {
            DB::statement(
                "UPDATE configuracion_sistema SET nombre_sistema=?, nombre_institucion=?, descripcion=?, localidad=?, provincia=?, pais=?, setup_completed=1, updated_at=NOW() WHERE id=1",
                ['Clínica Demo', 'Centro de Salud Mental Demo', 'Entorno de demostración — Historias Clínicas', 'Concepción del Uruguay', 'Entre Ríos', 'Argentina']
            );
        } else {
            DB::statement(
                "INSERT INTO configuracion_sistema (id,nombre_sistema,nombre_institucion,descripcion,localidad,provincia,pais,setup_completed,created_at,updated_at) VALUES (1,?,?,?,?,?,?,1,NOW(),NOW())",
                ['Clínica Demo', 'Centro de Salud Mental Demo', 'Entorno de demostración — Historias Clínicas', 'Concepción del Uruguay', 'Entre Ríos', 'Argentina']
            );
        }

        // ════════════════════════════════════════════
        //  ESPECIALIDADES
        // ════════════════════════════════════════════
        $especialidades = [
            ['Psicología Clínica',         'Evaluación y tratamiento de trastornos psicológicos'],
            ['Psiquiatría',                'Diagnóstico y tratamiento médico de trastornos mentales'],
            ['Salud Mental Comunitaria',   'Atención integral en contexto comunitario y familiar'],
            ['Neurología',                 'Diagnóstico de patologías del sistema nervioso central y periférico'],
            ['Trabajo Social',             'Intervención social, acompañamiento y orientación familiar'],
        ];
        foreach ($especialidades as [$nombre, $desc]) {
            if (!DB::table('especialidades')->where('nombre', $nombre)->whereNull('deleted_at')->exists()) {
                DB::table('especialidades')->insert([
                    'nombre'      => $nombre,
                    'descripcion' => $desc,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
        $espPsi    = DB::table('especialidades')->where('nombre', 'Psicología Clínica')->value('id');
        $espPsiq   = DB::table('especialidades')->where('nombre', 'Psiquiatría')->value('id');
        $espSalud  = DB::table('especialidades')->where('nombre', 'Salud Mental Comunitaria')->value('id');
        $espNeuro  = DB::table('especialidades')->where('nombre', 'Neurología')->value('id');

        // ════════════════════════════════════════════
        //  TIPOS DE INFORME
        // ════════════════════════════════════════════
        $tiposInforme = [
            'Informe Psicológico',
            'Informe Psiquiátrico',
            'Informe de Operador Terapéutico',
            'Informe Judicial',
            'Informe Clínico General',
        ];
        foreach ($tiposInforme as $tipo) {
            if (!DB::table('informes_tipos')->where('name', $tipo)->whereNull('deleted_at')->exists()) {
                DB::table('informes_tipos')->insert([
                    'name'       => $tipo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $tipoPs   = DB::table('informes_tipos')->where('name', 'Informe Psicológico')->value('id');
        $tipoPsiq = DB::table('informes_tipos')->where('name', 'Informe Psiquiátrico')->value('id');
        $tipoClin = DB::table('informes_tipos')->where('name', 'Informe Clínico General')->value('id');

        // ════════════════════════════════════════════
        //  PACIENTES
        // ════════════════════════════════════════════
        $pacientesData = [
            ['María José', 'López',     '30567891', '1985-03-15', 'Femenino',  40, 'Soltera',    'OSDE',   '112233', 'Entre Ríos', 'Concepción del Uruguay', 'Av. General Paz', '1450', '3442123456'],
            ['Carlos',     'Fernández', '22345678', '1972-07-22', 'Masculino', 52, 'Casado',     'PAMI',   '445566', 'Entre Ríos', 'Gualeguaychú',           'Belgrano',        '234',  '3446789012'],
            ['Ana Paula',  'Gómez',     '38901234', '1990-11-08', 'Femenino',  34, 'Divorciada', 'Galeno', '778899', 'Entre Ríos', 'Concordia',              'San Martín',      '567',  '3455234567'],
        ];
        foreach ($pacientesData as $p) {
            if (!DB::table('pacientes')->where('dni', $p[2])->exists()) {
                DB::table('pacientes')->insert([
                    'nombre'       => $p[0], 'apellido'     => $p[1], 'dni'          => $p[2],
                    'fecha_nac'    => $p[3], 'sexo'         => $p[4], 'edad'         => $p[5],
                    'estado_civil' => $p[6], 'obra_social'  => $p[7], 'n_afiliado'   => $p[8],
                    'provincia'    => $p[9], 'localidad'    => $p[10],'calle'         => $p[11],
                    'calle_numero' => $p[12],'telefono'     => $p[13],'status'        => 1,
                    'created_at'   => now(), 'updated_at'   => now(),
                ]);
            }
        }
        $p1 = DB::table('pacientes')->where('dni', '30567891')->value('id');
        $p2 = DB::table('pacientes')->where('dni', '22345678')->value('id');
        $p3 = DB::table('pacientes')->where('dni', '38901234')->value('id');

        // ════════════════════════════════════════════
        //  FICHAS DE ADMISIÓN
        // ════════════════════════════════════════════
        $fichas = [
            [$p1, '2024-03-15', 'ambulatorio',  null,         null],
            [$p2, '2024-01-10', 'ambulatorio',  null,         null],
            [$p3, '2024-05-20', 'ambulatorio',  null,         null],
        ];
        foreach ($fichas as [$pid, $ingreso, $modalidad, $egreso, $tipoEgreso]) {
            if (!DB::table('pacientes_ficha_admision')->where('paciente_id', $pid)->exists()) {
                DB::table('pacientes_ficha_admision')->insert([
                    'paciente_id'  => $pid,
                    'fecha_ingreso'=> $ingreso,
                    'modalidad'    => $modalidad,
                    'fecha_egreso' => $egreso,
                    'tipo_egreso'  => $tipoEgreso,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  EDUCACIÓN
        // ════════════════════════════════════════════
        $educ = [
            [$p1, 'Si', 'Si', null, 'No', 'No', null, 'No', 'No', 'No', null],
            [$p2, 'Si', 'Si', null, 'No', 'No', null, 'Si', 'No', null, null],
            [$p3, 'Si', 'Si', null, 'No', 'No', null, 'Si', 'Si', null, null],
        ];
        foreach ($educ as [$pid, $primComp, $secComp, $secIncomp, $secExpul, $secInter, $terComp, $facComp, $facIncomp, $facUlt, $obs]) {
            if (!DB::table('pacientes_educacion')->where('paciente_id', $pid)->whereNull('deleted_at')->exists()) {
                DB::table('pacientes_educacion')->insert([
                    'paciente_id'           => $pid,
                    'primaria_completa'     => $primComp,
                    'secundaria_completa'   => $secComp,
                    'secundaria_incompleta' => $secIncomp,
                    'secundaria_expulsado'  => $secExpul,
                    'secundaria_interrumpido'=> $secInter,
                    'terciaria_completa'    => $terComp,
                    'facultad_completa'     => $facComp,
                    'facultad_incompleta'   => $facIncomp,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  LABORAL
        // ════════════════════════════════════════════
        $laborales = [
            [$p1, 'Empleada', 'Municipalidad de Concepción del Uruguay', 'Administrativa', '8 años'],
            [$p2, 'Jubilado', null,                                       null,             null],
            [$p3, 'Empleada', 'Escuela Primaria N° 12',                  'Maestra',        '5 años'],
        ];
        foreach ($laborales as [$pid, $actividad, $empresa, $cargo, $antiguedad]) {
            if (!DB::table('pacientes_laboral')->where('paciente_id', $pid)->whereNull('deleted_at')->exists()) {
                DB::table('pacientes_laboral')->insert([
                    'paciente_id'       => $pid,
                    'actividad_laboral' => $actividad,
                    'empresa_laboral'   => $empresa,
                    'cargo_laboral'     => $cargo,
                    'antiguedad_laboral'=> $antiguedad,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  PROBLEMÁTICA
        // ════════════════════════════════════════════
        $problematicas = [
            [$p1, 'Ansiedad', 'Presenta cuadro de ansiedad generalizada con insomnio de conciliación, irritabilidad y preocupaciones excesivas. Refiere que los síntomas se intensificaron tras conflictos laborales.'],
            [$p2, 'Trastorno bipolar', 'Episodios maníacos y depresivos cíclicos. En el último episodio maníaco presentó desinhibición sexual, gastos excesivos y reducción drástica del sueño durante dos semanas.'],
            [$p3, 'Depresión', 'Episodio depresivo moderado asociado a separación conyugal. Anhedonia, hipersomnia, pérdida de apetito y sentimientos de culpa sin ideación suicida activa.'],
        ];
        foreach ($problematicas as [$pid, $prob, $det]) {
            if (!DB::table('pacientes_problematica')->where('paciente_id', $pid)->whereNull('deleted_at')->exists()) {
                DB::table('pacientes_problematica')->insert([
                    'paciente_id'           => $pid,
                    'problematica'          => $prob,
                    'problematica_detalles' => $det,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  USUARIOS DE PANEL
        // ════════════════════════════════════════════
        $adminId  = DB::table('users')->whereRaw("email LIKE 'demo@%'")->value('id') ?? 1;
        $roleUser = DB::table('roles')->where('title', 'User')->value('id') ?? 2;

        $panelUsers = [
            ['Dra. Laura Martínez',  'psicologa@demo.com',  [$espPsi, $espSalud]],
            ['Dr. Marcos Rodríguez', 'psiquiatra@demo.com', [$espPsiq, $espNeuro]],
        ];
        foreach ($panelUsers as [$nombre, $email, $esps]) {
            if (!DB::table('users')->where('email', $email)->exists()) {
                $uid = DB::table('users')->insertGetId([
                    'name'       => $nombre,
                    'email'      => $email,
                    'password'   => bcrypt('demo1234'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('role_user')->insert(['user_id' => $uid, 'role_id' => $roleUser]);
                foreach ($esps as $eid) {
                    if ($eid) {
                        DB::table('especialidad_user')->insert(['user_id' => $uid, 'especialidad_id' => $eid]);
                    }
                }
            }
        }
        $lauraId  = DB::table('users')->where('email', 'psicologa@demo.com')->value('id');
        $marcosId = DB::table('users')->where('email', 'psiquiatra@demo.com')->value('id');

        // ════════════════════════════════════════════
        //  MEDICACIONES
        // ════════════════════════════════════════════
        $hoy = now()->format('Y-m-d');
        $meds = [
            // María José López — ansiedad
            [$p1, $hoy, 'Sertralina',       '50',   'mg',  'mañana'],
            [$p1, $hoy, 'Clonazepam',       '0.5',  'mg',  'noche'],
            [$p1, $hoy, 'Olanzapina',       '5',    'mg',  'noche'],
            // Carlos Fernández — bipolar
            [$p2, $hoy, 'Litio',            '300',  'mg',  'mañana'],
            [$p2, $hoy, 'Litio',            '300',  'mg',  'noche'],
            [$p2, $hoy, 'Quetiapina',       '100',  'mg',  'noche'],
            [$p2, $hoy, 'Ácido Valpróico',  '500',  'mg',  'mañana'],
            // Ana Paula Gómez — depresión
            [$p3, $hoy, 'Fluoxetina',       '20',   'mg',  'mañana'],
            [$p3, $hoy, 'Alprazolam',       '0.25', 'mg',  'noche'],
            [$p3, $hoy, 'Trazodona',        '50',   'mg',  'noche'],
        ];
        foreach ($meds as [$pid, $fecha, $med, $cant, $unidad, $horario]) {
            if (!DB::table('medicaciones')->where('paciente_id', $pid)->where('fecha', $fecha)->where('medicamento', $med)->exists()) {
                DB::table('medicaciones')->insert([
                    'paciente_id' => $pid,
                    'fecha'       => $fecha,
                    'medicamento' => $med,
                    'cantidad'    => $cant,
                    'unidad'      => $unidad,
                    'horario'     => $horario,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  AGENDA
        // ════════════════════════════════════════════
        if (!DB::table('agendas')->where('paciente_id', $p1)->exists()) {
            $agendas = [
                // María José — Psicología
                [$p1, $espPsi, $lauraId,  'presencial', '2025-06-10 10:00:00', '2025-06-10 11:00:00', 'Consulta psicológica inicial',       'realizado',  'Paciente derivada desde médico clínico. Se inicia proceso de evaluación.'],
                [$p1, $espPsi, $lauraId,  'presencial', '2025-06-24 14:00:00', '2025-06-24 15:00:00', 'Seguimiento quincenal',               'confirmado', null],
                [$p1, $espPsi, $lauraId,  'virtual',    '2025-07-08 10:00:00', '2025-07-08 11:00:00', 'Control mensual — sesión virtual',    'pendiente',  null],
                [$p1, $espPsi, $lauraId,  'presencial', '2025-07-22 10:00:00', '2025-07-22 11:00:00', 'Cierre de etapa evaluativa',          'pendiente',  null],
                // Carlos — Psiquiatría
                [$p2, $espPsiq, $marcosId,'presencial', '2025-06-05 09:00:00', '2025-06-05 09:30:00', 'Evaluación psiquiátrica inicial',     'realizado',  'Se ajusta medicación según evaluación clínica.'],
                [$p2, $espPsiq, $marcosId,'presencial', '2025-06-19 09:00:00', '2025-06-19 09:30:00', 'Control psiquiátrico — 2 semanas',    'realizado',  'Litemia en rango terapéutico. Se agrega VPA.'],
                [$p2, $espPsiq, $marcosId,'presencial', '2025-07-03 09:30:00', '2025-07-03 10:00:00', 'Control mensual con litemia',         'pendiente',  null],
                [$p2, $espPsiq, $marcosId,'virtual',    '2025-07-17 09:00:00', '2025-07-17 09:30:00', 'Seguimiento farmacológico virtual',   'pendiente',  null],
                // Ana Paula — Psicología
                [$p3, $espPsi, $lauraId,  'presencial', '2025-06-12 11:00:00', '2025-06-12 12:00:00', 'Primera consulta psicológica',        'realizado',  null],
                [$p3, $espPsi, $lauraId,  'presencial', '2025-06-26 11:00:00', '2025-06-26 12:00:00', 'Segunda consulta de seguimiento',     'confirmado', null],
                [$p3, $espPsiq,$marcosId, 'presencial', '2025-07-10 16:00:00', '2025-07-10 16:30:00', 'Interconsulta psiquiátrica',          'pendiente',  null],
                [$p3, $espPsi, $lauraId,  'presencial', '2025-07-24 11:00:00', '2025-07-24 12:00:00', 'Tercera sesión psicoterapéutica',     'pendiente',  null],
            ];
            foreach ($agendas as [$pid, $eid, $prof, $modal, $ini, $fin, $motivo, $estado, $notas]) {
                DB::table('agendas')->insert([
                    'paciente_id'          => $pid,
                    'especialidad_id'      => $eid,
                    'profesional_tipo'     => 'registrado',
                    'profesional_id'       => $prof,
                    'modalidad'            => $modal,
                    'fecha_hora_inicio'    => $ini,
                    'fecha_hora_fin'       => $fin,
                    'motivo'               => $motivo,
                    'estado'               => $estado,
                    'notas'                => $notas,
                    'recordatorio_enviado' => 0,
                    'creado_por'           => $adminId,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }
        }

        // ════════════════════════════════════════════
        //  INFORMES (con PDF generado)
        // ════════════════════════════════════════════
        $informesData = [
            // ── María José López — Psicológico ──────────────
            [
                'paciente_id' => $p1, 'tipo_id' => $tipoPs, 'profesional_id' => $lauraId,
                'fecha' => '2025-06-10', 'diagnostico' => 'Trastorno de ansiedad generalizada', 'codigo_cie10' => 'F41.1',
                'nombre_pac' => 'López, María José', 'dni_pac' => '30567891',
                'tipo_nombre' => 'Informe Psicológico', 'prof_nombre' => 'Dra. Laura Martínez',
                'contenido' => '<p>Se realizó evaluación psicológica inicial. La paciente presenta <strong>síntomas compatibles con trastorno de ansiedad generalizada (F41.1)</strong>, con preocupaciones excesivas y persistentes en múltiples ámbitos de su vida cotidiana.</p><p>Se observa tensión muscular, dificultad para concentrarse e irritabilidad marcada. Refiere insomnio de conciliación de aproximadamente tres semanas de evolución.</p><h3>Hallazgos relevantes</h3><ul><li>Preocupación excesiva difícil de controlar</li><li>Inquietud motora y sensación de estar al límite</li><li>Fatiga frecuente y alteraciones del sueño</li><li>Sin antecedentes psiquiátricos previos</li></ul><h3>Plan terapéutico</h3><p>Se indica inicio de proceso psicoterapéutico con frecuencia quincenal bajo enfoque cognitivo-conductual. Se derivan al psiquiatra para evaluación de farmacoterapia complementaria.</p>',
            ],
            [
                'paciente_id' => $p1, 'tipo_id' => $tipoPs, 'profesional_id' => $lauraId,
                'fecha' => '2025-06-24', 'diagnostico' => 'TAG — seguimiento quincenal', 'codigo_cie10' => 'F41.1',
                'nombre_pac' => 'López, María José', 'dni_pac' => '30567891',
                'tipo_nombre' => 'Informe Psicológico', 'prof_nombre' => 'Dra. Laura Martínez',
                'contenido' => '<p>Segunda consulta psicológica. La paciente refiere <strong>leve mejoría en la calidad del sueño</strong> desde el inicio del proceso terapéutico y del tratamiento farmacológico.</p><p>Se trabajaron técnicas de respiración diafragmática y se introdujeron registros de pensamientos automáticos. La paciente mostró buena disposición y comprensión de las técnicas.</p><p>Sin crisis de ansiedad reportadas en el período interconsulta. <strong>Evolución favorable.</strong> Se indica continuación del proceso con frecuencia quincenal.</p>',
            ],
            [
                'paciente_id' => $p1, 'tipo_id' => $tipoClin, 'profesional_id' => $adminId,
                'fecha' => '2025-06-17', 'diagnostico' => 'Control clínico de rutina', 'codigo_cie10' => null,
                'nombre_pac' => 'López, María José', 'dni_pac' => '30567891',
                'tipo_nombre' => 'Informe Clínico General', 'prof_nombre' => null,
                'contenido' => '<p>Control clínico de rutina. Paciente en buen estado general, lúcida, orientada en tiempo y espacio.</p><p><strong>Signos vitales:</strong> TA 120/80 mmHg · FC 76 lpm · FR 16 rpm · Temperatura 36.5 °C · Peso 62 kg · Talla 1.65 m.</p><p>Examen físico sin hallazgos patológicos relevantes. Se solicitan análisis de laboratorio de control (hemograma, glucemia, hepatograma, litemia).</p><p>Continúa con tratamiento farmacológico y psicoterapéutico. Próximo control en 30 días.</p>',
            ],
            // ── Carlos Fernández — Psiquiátrico ─────────────
            [
                'paciente_id' => $p2, 'tipo_id' => $tipoPsiq, 'profesional_id' => $marcosId,
                'fecha' => '2025-06-05', 'diagnostico' => 'Trastorno bipolar tipo I', 'codigo_cie10' => 'F31.1',
                'nombre_pac' => 'Fernández, Carlos', 'dni_pac' => '22345678',
                'tipo_nombre' => 'Informe Psiquiátrico', 'prof_nombre' => 'Dr. Marcos Rodríguez',
                'contenido' => '<p>Evaluación psiquiátrica inicial del paciente. <strong>Diagnóstico compatible con Trastorno Bipolar tipo I (F31.1)</strong>. El paciente refiere episodios maníacos de dos semanas de duración con aumento de la actividad, reducción del sueño a 2-3 horas diarias y comportamiento desinhibido con gastos excesivos.</p><p>También refiere episodios depresivos subsecuentes con anhedonia, hipersomnia e ideación pasiva de muerte.</p><h3>Plan farmacológico</h3><ul><li>Litio 300 mg cada 12 horas (mañana y noche)</li><li>Quetiapina 100 mg por la noche como estabilizador coadyuvante</li><li>Monitoreo de litemia a los 7 días y luego mensual</li></ul><p>Control psiquiátrico a las dos semanas.</p>',
            ],
            [
                'paciente_id' => $p2, 'tipo_id' => $tipoPsiq, 'profesional_id' => $marcosId,
                'fecha' => '2025-06-19', 'diagnostico' => 'Trastorno bipolar tipo I — control', 'codigo_cie10' => 'F31.1',
                'nombre_pac' => 'Fernández, Carlos', 'dni_pac' => '22345678',
                'tipo_nombre' => 'Informe Psiquiátrico', 'prof_nombre' => 'Dr. Marcos Rodríguez',
                'contenido' => '<p>Control a las dos semanas de inicio de medicación. El paciente refiere <strong>mejoría significativa del sueño</strong> (actualmente duerme 7-8 horas) y reducción de la actividad motora. Sin efectos adversos gastrointestinales del litio.</p><p><strong>Litemia:</strong> 0.8 mEq/L — dentro del rango terapéutico (0.6–1.2 mEq/L). Función renal y tiroidea normales.</p><p>Se agrega <strong>Ácido Valpróico 500 mg por la mañana</strong> como potenciador del estabilizador. Se mantiene Litio y Quetiapina sin cambios.</p><p>Próximo control en 30 días con nueva litemia.</p>',
            ],
            // ── Ana Paula Gómez — Psicológico ───────────────
            [
                'paciente_id' => $p3, 'tipo_id' => $tipoPs, 'profesional_id' => $lauraId,
                'fecha' => '2025-06-12', 'diagnostico' => 'Episodio depresivo moderado', 'codigo_cie10' => 'F32.1',
                'nombre_pac' => 'Gómez, Ana Paula', 'dni_pac' => '38901234',
                'tipo_nombre' => 'Informe Psicológico', 'prof_nombre' => 'Dra. Laura Martínez',
                'contenido' => '<p>Primera consulta psicológica. La paciente consulta por <strong>estado de ánimo deprimido de seis semanas de evolución</strong>, asociado a separación conyugal reciente.</p><p>Refiere anhedonia marcada, hipersomnia de 10-12 horas diarias, disminución del apetito con pérdida de 4 kg en el último mes, y sentimientos de culpa. <strong>No presenta ideación suicida activa ni antecedentes de intentos.</strong></p><h3>Diagnóstico presuntivo</h3><p>Episodio depresivo moderado (F32.1) en contexto de duelo por separación conyugal.</p><h3>Plan terapéutico</h3><ul><li>Psicoterapia individual de orientación cognitivo-conductual — frecuencia semanal</li><li>Interconsulta psiquiátrica para evaluación de farmacoterapia antidepresiva</li><li>Red de apoyo: fortalecer vínculos familiares</li></ul>',
            ],
        ];

        foreach ($informesData as $inf) {
            if (DB::table('informes')
                ->where('paciente_id', $inf['paciente_id'])
                ->where('tipo_id', $inf['tipo_id'])
                ->where('fecha', $inf['fecha'])
                ->exists()) {
                continue;
            }

            $documentFiles = null;
            try {
                $fechaFmt = Carbon::parse($inf['fecha'])->translatedFormat('d \d\e F \d\e Y');
                $pdfContent = Pdf::loadView('pdf.informe', [
                    'tipo'         => $inf['tipo_nombre'],
                    'fecha'        => $fechaFmt,
                    'paciente'     => $inf['nombre_pac'],
                    'pacienteDNI'  => $inf['dni_pac'],
                    'informe'      => $inf['contenido'],
                    'diagnostico'  => $inf['diagnostico'],
                    'codigo_cie10' => $inf['codigo_cie10'] ?? null,
                    'profesional'  => $inf['prof_nombre'] ?? null,
                ])->output();

                $filename  = Str::random(20) . '.pdf';
                $dir       = 'uploads/' . $inf['paciente_id'] . '/' . $inf['tipo_id'];
                Storage::disk('public')->makeDirectory($dir);
                Storage::disk('public')->put($dir . '/' . $filename, $pdfContent);
                $documentFiles = json_encode([$filename]);
            } catch (\Throwable $e) {
                // Continuar sin PDF si hay error de rendering
            }

            DB::table('informes')->insert([
                'paciente_id'    => $inf['paciente_id'],
                'tipo_id'        => $inf['tipo_id'],
                'profesional_id' => $inf['profesional_id'],
                'fecha'          => $inf['fecha'],
                'diagnostico'    => $inf['diagnostico'],
                'codigo_cie10'   => $inf['codigo_cie10'] ?? null,
                'document_files' => $documentFiles,
                'lock_version'   => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
