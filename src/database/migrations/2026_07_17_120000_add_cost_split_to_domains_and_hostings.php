<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename nativo por SQL (sin doctrine/dbal, no instalado en este proyecto — mismo
        // criterio ya usado para el nullable de contracts.tenant_id). 0 filas reales en
        // producción en ambas tablas hoy, así que el rename es seguro.
        DB::statement('ALTER TABLE client_domains RENAME COLUMN renewal_cost TO provider_cost');
        DB::statement('ALTER TABLE hostings RENAME COLUMN renewal_cost TO provider_cost');

        Schema::table('client_domains', function (Blueprint $table) {
            $table->decimal('management_fee', 12, 2)->nullable()->after('provider_cost');
        });

        Schema::table('hostings', function (Blueprint $table) {
            $table->decimal('management_fee', 12, 2)->nullable()->after('provider_cost');
        });

        Schema::table('client_services', function (Blueprint $table) {
            $table->decimal('provider_cost', 12, 2)->nullable()->default(0)->after('amount');
            $table->decimal('management_fee', 12, 2)->nullable()->after('provider_cost');
        });
    }

    public function down(): void
    {
        Schema::table('client_services', function (Blueprint $table) {
            $table->dropColumn(['provider_cost', 'management_fee']);
        });

        Schema::table('hostings', function (Blueprint $table) {
            $table->dropColumn('management_fee');
        });

        Schema::table('client_domains', function (Blueprint $table) {
            $table->dropColumn('management_fee');
        });

        DB::statement('ALTER TABLE hostings RENAME COLUMN provider_cost TO renewal_cost');
        DB::statement('ALTER TABLE client_domains RENAME COLUMN provider_cost TO renewal_cost');
    }
};
