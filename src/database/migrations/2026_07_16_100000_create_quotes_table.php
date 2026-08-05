<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('status')->default('borrador'); // borrador|enviada|aceptada|rechazada
            $table->date('valid_until')->nullable();
            $table->boolean('creates_project')->default(false);
            $table->string('project_name')->nullable();
            $table->string('project_type')->nullable();
            $table->decimal('initial_charge_amount', 12, 2)->nullable();
            $table->string('public_token')->nullable()->unique();
            $table->timestamp('public_token_expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
