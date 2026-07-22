<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_laborales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 30); // preocupacional|periodico|egreso
            $table->date('fecha');
            $table->string('estado', 30); // apto|no_apto|apto_con_restricciones
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_laborales');
    }
};
