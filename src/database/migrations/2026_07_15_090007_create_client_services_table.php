<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('service_type'); // mantenimiento|seo|google_ads|backup|desarrollo_a_medida|consultoria|otro
            $table->string('billing_cycle'); // mensual|unico
            $table->decimal('amount', 12, 2);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->string('status')->default('active'); // active|cancelled|completed
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_services');
    }
};
