<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmaAuditoriasTable extends Migration
{
    public function up()
    {
        Schema::create('firma_auditorias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informe_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('firmado_at');
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('version_documento')->default(1);
            $table->timestamps();

            $table->foreign('informe_id')->references('id')->on('informes')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('firma_auditorias');
    }
}
