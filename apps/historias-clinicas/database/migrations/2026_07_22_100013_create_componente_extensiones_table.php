<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componente_extensiones', function (Blueprint $table) {
            $table->id();
            $table->string('componente_key', 100)->unique();
            $table->string('extension_key', 150); // FQCN de la ComponenteExtension
            $table->string('version', 50);
            $table->timestamp('instalado_en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componente_extensiones');
    }
};
