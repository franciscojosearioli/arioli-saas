<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes_datos_adicionales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('paciente_id');
            $table->string('abuso_sexual')->nullable();
            $table->string('sobredosis')->nullable();
            $table->string('antecedentes_legales')->nullable();
            $table->string('analfabeto')->nullable();
            $table->string('padres_separados')->nullable();
            $table->string('privado_libertad')->nullable();
            $table->string('tiempo_privado_libertad')->nullable();
            $table->string('enfermedad_cronica')->nullable();
            $table->text('enfermedad_cronica_detalles')->nullable();
            $table->string('alergia')->nullable();
            $table->text('alergia_detalles')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes_datos_adicionales');
    }
};
