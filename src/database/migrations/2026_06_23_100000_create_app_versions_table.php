<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('version', 20);
            $table->enum('type', ['stable', 'beta', 'alpha'])->default('stable');
            $table->json('changelog');
            $table->timestamp('released_at');
            $table->boolean('is_current')->default(false);
            $table->string('min_php_version', 10)->default('8.3');
            $table->string('docker_tag', 100)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'version'], 'uq_product_version');
            $table->index(['product_id', 'is_current'], 'idx_product_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
