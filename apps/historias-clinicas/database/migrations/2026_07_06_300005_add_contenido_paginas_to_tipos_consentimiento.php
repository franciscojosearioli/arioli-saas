<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_consentimiento', function (Blueprint $table) {
            $table->json('contenido_paginas')->nullable()->after('contenido_pagina2');
        });

        // Migrate existing p1/p2 columns into the new JSON array
        DB::table('tipos_consentimiento')->get()->each(function ($tipo) {
            $paginas = [];
            if (!empty($tipo->contenido_pagina1)) {
                $paginas[] = $tipo->contenido_pagina1;
            }
            if (!empty($tipo->contenido_pagina2)) {
                $paginas[] = $tipo->contenido_pagina2;
            }
            if (empty($paginas)) {
                $paginas = [''];
            }

            DB::table('tipos_consentimiento')
                ->where('id', $tipo->id)
                ->update(['contenido_paginas' => json_encode($paginas)]);
        });
    }

    public function down(): void
    {
        Schema::table('tipos_consentimiento', function (Blueprint $table) {
            $table->dropColumn('contenido_paginas');
        });
    }
};
