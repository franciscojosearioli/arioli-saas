<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Rev. 1.3 del Artifact: un lead que llegaba por "marketplace" ahora
// llega por el storefront propio del tenant — mismo origen real, nombre
// más preciso ahora que no hay un servicio de marketplace aparte.
// Schema::change() (no DB::statement crudo) para que corra igual en
// sqlite (tests) que en MySQL — sqlite no soporta ALTER MODIFY, pero
// Laravel reconstruye la tabla por dentro para un enum() ->change().
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('origen', ['marketplace', 'storefront', 'formulario', 'whatsapp', 'referido', 'otro'])
                ->default('otro')->change();
        });

        DB::table('leads')->where('origen', 'marketplace')->update(['origen' => 'storefront']);

        Schema::table('leads', function (Blueprint $table) {
            $table->enum('origen', ['storefront', 'formulario', 'whatsapp', 'referido', 'otro'])
                ->default('otro')->change();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('origen', ['marketplace', 'storefront', 'formulario', 'whatsapp', 'referido', 'otro'])
                ->default('otro')->change();
        });

        DB::table('leads')->where('origen', 'storefront')->update(['origen' => 'marketplace']);

        Schema::table('leads', function (Blueprint $table) {
            $table->enum('origen', ['marketplace', 'formulario', 'whatsapp', 'referido', 'otro'])
                ->default('otro')->change();
        });
    }
};
