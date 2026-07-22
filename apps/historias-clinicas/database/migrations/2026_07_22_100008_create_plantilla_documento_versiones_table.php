<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_documento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_documento_id')->constrained('plantillas_documento')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('contenido');
            $table->json('variables_disponibles')->nullable();
            $table->timestamp('vigente_desde');
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['plantilla_documento_id', 'version'], 'plantilla_doc_version_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_documento_versiones');
    }
};
