<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.4 (ver docs/ARQUITECTURA_MODULAR.md): separa el identificador
 * interno (tenant_key, nunca visible, sufijo de la base de datos) del
 * identificador público (slug, solo caracteres válidos en un hostname
 * DNS, usado para resolver el tenant por subdominio). Antes de esto,
 * tenant_key cumplía los dos roles a la vez — funcionaba porque ningún
 * tenant_key real había usado guión bajo todavía, no porque el diseño lo
 * garantizara.
 *
 * Backfill incluido: hoy solo existen 2 filas reales (`demo`, `default`),
 * y ambos tenant_key ya son slugs válidos tal cual — no hace falta
 * generar nada nuevo para ellas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->unique()->after('tenant_key');
        });

        DB::statement('UPDATE tenants SET slug = tenant_key WHERE slug IS NULL');
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
