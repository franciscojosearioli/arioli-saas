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
        Schema::create('pacientes_educacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('paciente_id');
            $table->string('primaria_completa')->nullable();
            $table->string('primaria_incompleta')->nullable();
            $table->string('primaria_ultimo_anio')->nullable();
            $table->string('primaria_expulsado')->nullable();
            $table->string('primaria_interrumpido')->nullable();
            $table->string('primaria_cambios')->nullable();
            $table->string('secundaria_completa')->nullable();
            $table->string('secundaria_incompleta')->nullable();
            $table->string('secundaria_ultimo_anio')->nullable();
            $table->string('secundaria_expulsado')->nullable();
            $table->string('secundaria_interrumpido')->nullable();
            $table->string('secundaria_cambios')->nullable();
            $table->string('terciaria_completa')->nullable();
            $table->string('terciaria_incompleta')->nullable();
            $table->string('terciaria_ultimo_anio')->nullable();
            $table->string('terciaria_expulsado')->nullable();
            $table->string('terciaria_interrumpido')->nullable();
            $table->string('terciaria_cambios')->nullable();
            $table->string('facultad_completa')->nullable();
            $table->string('facultad_incompleta')->nullable();
            $table->string('facultad_ultimo_anio')->nullable();
            $table->string('facultad_expulsado')->nullable();
            $table->string('facultad_interrumpido')->nullable();
            $table->string('facultad_cambios')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes_educacion');
    }
};