<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecetasTable extends Migration
{
    public function up()
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informe_id');
            $table->string('archivo');
            $table->string('nombre_original');
            $table->string('tipo_mime', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('informe_id')->references('id')->on('informes')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recetas');
    }
}
