<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_visibility', function (Blueprint $table) {
            $table->id();
            $table->string('entidad', 100);
            $table->string('campo', 100);
            $table->enum('tipo', ['campo', 'seccion', 'tab']);
            $table->boolean('visible')->default(true);
            $table->boolean('requerido')->nullable();
            $table->string('origen', 20)->default('preset'); // 'preset' | 'manual'
            $table->timestamps();

            $table->unique(['entidad', 'campo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visibility');
    }
};
