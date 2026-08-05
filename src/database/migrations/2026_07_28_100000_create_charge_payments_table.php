<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pagos parciales contra un Charge. Permite cobrar un cobro informal
     * (efectivo/transferencia, sin Mercado Pago) de a partes — ej. un
     * trabajo de $1000 que se va saldando con pagos de $200, $300, etc.
     * hasta completar el monto. Charge::markAsPaid() (el flujo clásico de
     * pago único) también inserta acá un registro por el monto completo,
     * así amountPaid()/balance() son consistentes sin importar el camino.
     */
    public function up(): void
    {
        Schema::create('charge_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->date('paid_at');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('charge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charge_payments');
    }
};
