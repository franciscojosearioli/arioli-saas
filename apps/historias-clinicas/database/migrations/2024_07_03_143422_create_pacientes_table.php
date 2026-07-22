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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->unique();
            $table->date('fecha_nac');
            $table->string('sexo');
            $table->integer('edad');
            $table->string('estado_civil');
            $table->string('obra_social');
            $table->string('n_afiliado');
            $table->string('provincia');
            $table->string('localidad');
            $table->string('calle');
            $table->string('calle_numero');
            $table->string('calle_piso')->nullable();
            $table->string('calle_dpto')->nullable();
            $table->string('familiar_hijos')->nullable();
            $table->string('familiar_hermanos')->nullable();
            $table->string('historial_tratamiento')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('pacientes');
    }
};
