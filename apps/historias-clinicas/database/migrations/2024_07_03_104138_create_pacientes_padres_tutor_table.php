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
        Schema::create('pacientes_padres_tutor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('paciente_id');
            $table->string('padre_nombre')->nullable();
            $table->string('padre_telefono')->nullable();
            $table->string('padre_responsable')->nullable();
            $table->string('madre_nombre')->nullable();
            $table->string('madre_telefono')->nullable();
            $table->string('madre_responsable')->nullable();
            $table->string('tutor_nombre')->nullable();
            $table->string('tutor_telefono')->nullable();
            $table->string('tutor_responsable')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes_padres_tutor');
    }
};