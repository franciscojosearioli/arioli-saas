<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_sistema', function (Blueprint $table) {
            $table->boolean('setup_completed')->default(false)->after('pie_pdf');
        });

        // Los registros existentes ya estaban configurados antes del wizard — marcarlos como completos.
        DB::table('configuracion_sistema')->update(['setup_completed' => true]);
    }

    public function down(): void
    {
        Schema::table('configuracion_sistema', function (Blueprint $table) {
            $table->dropColumn('setup_completed');
        });
    }
};
