<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Rev. 1.3 del Artifact: el marketplace cross-tenant se decomisiona — el
// storefront propio del tenant ya no es un "canal" (se muestra en cuanto
// existe la Publicación, sin adapter). 'sitio_web' es hoy el único canal
// externo real; Facebook/Instagram se suman con su propia migración en
// Fase 4, mismo criterio que la migración original.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('publicacion_canales')->where('canal', 'marketplace')->delete();

        Schema::table('publicacion_canales', function (Blueprint $table) {
            $table->enum('canal', ['sitio_web'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('publicacion_canales', function (Blueprint $table) {
            $table->enum('canal', ['marketplace', 'sitio_web'])->change();
        });
    }
};
