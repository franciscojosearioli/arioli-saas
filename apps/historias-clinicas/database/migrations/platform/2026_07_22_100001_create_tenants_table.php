<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_key', 100)->unique();
            $table->string('database', 100);
            $table->enum('status', ['activo', 'suspendido', 'en_migracion', 'error'])->default('activo');
            $table->string('version', 20)->nullable();
            $table->timestamp('last_migration_at')->nullable();
            $table->string('last_migration_status', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
