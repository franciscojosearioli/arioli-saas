<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes_reingresos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('paciente_id');
            $table->date('fecha_reingreso')->nullable();
            $table->string('modalidad')->nullable();
            $table->date('fecha_egreso')->nullable();
            $table->string('tipo_egreso')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes_reingresos');
    }
};
