<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piezas_dentales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero'); // notación FDI: 11-18, 21-28, 31-38, 41-48
            $table->string('estado', 30)->default('sana'); // sana|cariada|obturada|ausente|extraida|corona|implante
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['odontograma_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piezas_dentales');
    }
};
