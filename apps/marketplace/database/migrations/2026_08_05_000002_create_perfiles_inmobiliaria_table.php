<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: "perfil de inmobiliaria — una página por tenant, con su branding".
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles_inmobiliaria', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->string('slug')->unique();
            $table->string('nombre_comercial');
            $table->text('descripcion')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles_inmobiliaria');
    }
};
