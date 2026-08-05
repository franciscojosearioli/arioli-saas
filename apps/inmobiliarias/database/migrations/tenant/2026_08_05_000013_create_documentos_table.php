<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04/§14: "documentación adjunta" de Fase 2 — adjunto polimórfico simple
// a Propiedad, Operación, Contrato o Cliente, con vencimiento y versión.
// El DMS completo (SolicitudDeFirma, Firma, firma electrónica/digital con
// validez jurídica — §10) es Fase 6; acá no hay todavía workflow de firma,
// solo el archivo y sus metadatos.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->foreignId('subido_por_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->enum('tipo', ['boleto', 'escritura', 'dni', 'comprobante', 'contrato', 'otro']);
            $table->string('nombre');
            $table->string('archivo');
            $table->date('fecha_vencimiento')->nullable();
            $table->unsignedInteger('version')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
