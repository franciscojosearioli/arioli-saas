<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->unsignedBigInteger('especialidad_id')->nullable()->after('paciente_id');
            $table->foreign('especialidad_id')->references('id')->on('especialidades')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn('especialidad_id');
        });
    }
};
