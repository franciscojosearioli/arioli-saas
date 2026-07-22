<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendasTable extends Migration
{
    public function up()
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->enum('profesional_tipo', ['registrado', 'externo'])->default('registrado');
            $table->foreignId('profesional_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('profesional_externo_nombre')->nullable();
            $table->string('profesional_externo_email')->nullable();
            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial');
            $table->dateTime('fecha_hora_inicio');
            $table->dateTime('fecha_hora_fin');
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'confirmado', 'cancelado', 'realizado'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->string('link_virtual')->nullable();
            $table->boolean('recordatorio_enviado')->default(false);
            $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendas');
    }
}
