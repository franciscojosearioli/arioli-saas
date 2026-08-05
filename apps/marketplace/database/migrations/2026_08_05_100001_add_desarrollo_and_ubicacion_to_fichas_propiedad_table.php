<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: cierra el mismo hueco que la tabla `desarrollos` — la ficha
// necesita su propia ubicación (point o polygon, §04) para poder
// dibujarse coloreada en el mapa del desarrollo, y un vínculo real (no un
// nombre suelto) a la fila de `desarrollos` que la agrupa.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fichas_propiedad', function (Blueprint $table) {
            $table->foreignId('desarrollo_id')->nullable()->after('nombre_desarrollo')
                ->constrained('desarrollos')->nullOnDelete();
            $table->geometry('ubicacion')->nullable()->after('galeria');
        });
    }

    public function down(): void
    {
        Schema::table('fichas_propiedad', function (Blueprint $table) {
            $table->dropConstrainedForeignId('desarrollo_id');
            $table->dropColumn('ubicacion');
        });
    }
};
