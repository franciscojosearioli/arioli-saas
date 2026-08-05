<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('marketing_description')->nullable();
            $table->json('specs_json')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('ARS');
            $table->string('billing_cycle')->default('mensual'); // BillingCycle enum
            $table->boolean('active')->default(true);
            $table->string('hestia_package')->nullable();
            $table->timestamps();

            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_plans');
    }
};
