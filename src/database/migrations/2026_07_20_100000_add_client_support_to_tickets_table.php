<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los tickets solo servían al portal SaaS (siempre atados a un Tenant) — los
 * clientes de Hosting/Dominio (`client_id`, sin licencia) no podían crear
 * tickets ni en la BD (tenant_id era NOT NULL) ni en la policy (TicketPolicy
 * negaba explícitamente a cualquiera sin tenant_id). doctrine/dbal no está
 * instalado, así que tenant_id se afloja con SQL nativo, igual que ya se hizo
 * en contracts (ver 2026_07_16_100002).
 *
 * `related_type`/`related_id` es opcional: permite vincular el ticket a un
 * activo puntual del cliente (Hosting, ClientDomain, ClientService) para que
 * soporte sepa de qué está hablando sin tener que preguntarlo.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tickets MODIFY tenant_id VARCHAR(255) NULL');

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('tenant_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('related');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropMorphs('related');
        });

        DB::statement("ALTER TABLE tickets MODIFY tenant_id VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
