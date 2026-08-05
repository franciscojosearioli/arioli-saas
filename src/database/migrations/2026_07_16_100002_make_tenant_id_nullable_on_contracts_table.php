<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * contracts.tenant_id era NOT NULL desde que Legales solo servía contratos de
 * licencia/orden (siempre atados a un Tenant). Desde la Etapa 1 del CRM, un
 * Contract puede pertenecer a un Client sin ningún Tenant (contrato general,
 * o de un servicio/proyecto sin licencia) — necesita poder quedar null.
 * doctrine/dbal no está instalado, así que se usa SQL nativo en vez de
 * ->nullable()->change().
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE contracts MODIFY tenant_id VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contracts MODIFY tenant_id VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
