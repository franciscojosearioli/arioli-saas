<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Plan de cuotas sugerido — puramente informativo (ej. "5 cuotas de
     * $200"), no se hace cumplir: los pagos reales via ChargePayment pueden
     * ser de cualquier monto y en cualquier orden hasta saldar el total.
     */
    public function up(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->unsignedInteger('installments_count')->nullable()->after('due_date');
            $table->decimal('installment_amount', 12, 2)->nullable()->after('installments_count');
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn(['installments_count', 'installment_amount']);
        });
    }
};
