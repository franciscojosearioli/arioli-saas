<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentes_instalados', function (Blueprint $table) {
            $table->id();
            $table->string('componente_key', 100)->unique();
            $table->timestamp('instalado_en');
            $table->foreignId('instalado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentes_instalados');
    }
};
