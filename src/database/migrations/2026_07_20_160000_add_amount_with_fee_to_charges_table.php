<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El link de pago de Mercado Pago cobra `amount` + la comisión configurada
 * (mercadopago.fee_percent) para que a Arioli le quede el monto original neto.
 * Se guarda el monto ya calculado (no se recalcula en la vista del mail/portal)
 * para que lo que se muestra sea exactamente lo que Mercado Pago va a cobrar,
 * sin arriesgar un desfasaje de redondeo entre dos cálculos separados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->decimal('amount_with_fee', 12, 2)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn('amount_with_fee');
        });
    }
};
