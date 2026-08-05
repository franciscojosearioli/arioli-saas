<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Las facturas generadas desde un Charge del CRM no tienen tenant SaaS
 * asociado — `tenant_id` era NOT NULL porque hasta ahora solo se facturaban
 * Orders (licencias). Se usa SQL crudo (no Blueprint::change()) para no
 * agregar la dependencia de doctrine/dbal solo por esto.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE invoices MODIFY tenant_id VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE invoices MODIFY tenant_id VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
