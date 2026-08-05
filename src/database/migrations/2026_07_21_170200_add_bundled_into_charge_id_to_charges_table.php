<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Una "orden de pago" agrupa varios Charges pendientes en uno solo nuevo
 * (con un único link de Mercado Pago) sin perder el historial de los
 * originales — quedan marcados con `bundled_into_charge_id` apuntando al
 * Charge combinado, y se marcan pagados en cascada cuando ese se paga.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->foreignId('bundled_into_charge_id')->nullable()->after('invoice_id')->constrained('charges')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bundled_into_charge_id');
        });
    }
};
