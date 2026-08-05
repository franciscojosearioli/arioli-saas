<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('chargeable');
            $table->string('concept');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('ARS');
            $table->string('status')->default('pending'); // pending|paid|rejected|cancelled
            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('mp_payment_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
