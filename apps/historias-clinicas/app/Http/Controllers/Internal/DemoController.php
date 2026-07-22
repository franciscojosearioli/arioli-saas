<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Database\Seeders\DemoSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoController extends Controller
{
    public function seed(): JsonResponse
    {
        (new DemoSeeder)->run();
        return response()->json(['ok' => true]);
    }

    public function reset(): JsonResponse
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        foreach ([
            'pacientes_ficha_admision', 'pacientes_padres_tutor', 'pacientes_conyugues',
            'pacientes_hijos', 'pacientes_hermanos', 'pacientes_educacion',
            'pacientes_laboral', 'pacientes_historial', 'pacientes_problematica',
            'pacientes_datos_adicionales', 'informes', 'agendas', 'pacientes',
        ] as $table) {
            try { DB::statement("TRUNCATE TABLE {$table}"); } catch (\Throwable) {}
        }
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
        DB::statement(
            "UPDATE users SET password=?,updated_at=NOW() WHERE id IN (SELECT id FROM (SELECT id FROM users ORDER BY id LIMIT 1) t)",
            [Hash::make('demo1234')]
        );
        (new DemoSeeder)->run();
        return response()->json(['ok' => true]);
    }
}
