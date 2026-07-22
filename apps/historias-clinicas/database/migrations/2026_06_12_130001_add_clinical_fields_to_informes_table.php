<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->unsignedBigInteger('profesional_id')->nullable()->after('tipo_id');
            $table->unsignedBigInteger('agenda_id')->nullable()->after('profesional_id');
            $table->string('diagnostico')->nullable()->after('fecha');
            $table->string('codigo_cie10', 10)->nullable()->after('diagnostico');

            $table->foreign('profesional_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('agenda_id')->references('id')->on('agendas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->dropForeign(['profesional_id']);
            $table->dropForeign(['agenda_id']);
            $table->dropColumn(['profesional_id', 'agenda_id', 'diagnostico', 'codigo_cie10']);
        });
    }
};
