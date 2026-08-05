<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rebrand "Sistema de Salud": el checkout necesita capturar qué variante
 * (perfil) del producto historias-clinicas contrata el cliente — nullable
 * porque otros productos (loteos, tallerpro) no tienen perfil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('perfil')->nullable()->after('customer_company');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('perfil');
        });
    }
};
