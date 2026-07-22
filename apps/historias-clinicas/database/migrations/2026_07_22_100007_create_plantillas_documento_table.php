<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantillas_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documento_id')->constrained('informes_tipos')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('codigo', 100)->unique();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_documento');
    }
};
