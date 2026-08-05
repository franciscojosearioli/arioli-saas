<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->nullOnDelete();
            $table->string('period')->after('name')->default('monthly'); // monthly, quarterly, semiannual, annual
            $table->integer('period_months')->after('period')->default(1);
            $table->decimal('base_price', 12, 2)->after('period_months');
            $table->integer('discount_percent')->after('base_price')->default(0);
            $table->dropColumn(['max_users', 'max_branches']);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'period', 'period_months', 'base_price', 'discount_percent']);
            $table->integer('max_users')->nullable();
            $table->integer('max_branches')->nullable();
        });
    }
};