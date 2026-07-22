<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('especialidad_user', function (Blueprint $table) {
            $table->unsignedBigInteger('especialidad_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['especialidad_id', 'user_id']);

            $table->foreign('especialidad_id')->references('id')->on('especialidades')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_user');
        Schema::dropIfExists('especialidades');
    }
};
