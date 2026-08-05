<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rebrand comercial: "Historias Clínicas" → "Sistema de Salud" (ver
 * docs/ARQUITECTURA_MODULAR.md). No se edita la migración original
 * (2026_06_12_100000) para no reescribir historia ya aplicada — se
 * cambia el DEFAULT de la columna acá, y se corre `tenants:migrate`
 * para que los tenants existentes también lo reciban.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE configuracion_sistema MODIFY nombre_sistema VARCHAR(255) NOT NULL DEFAULT 'Sistema de Salud'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE configuracion_sistema MODIFY nombre_sistema VARCHAR(255) NOT NULL DEFAULT 'Historias Clínicas'");
    }
};
