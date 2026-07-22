<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capability_states', function (Blueprint $table) {
            $table->id();
            $table->string('capability_key', 150)->unique();
            $table->boolean('enabled')->default(false);
            $table->string('source', 20)->default('preset'); // 'preset' | 'manual'
            $table->json('settings')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->foreignId('enabled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_states');
    }
};
