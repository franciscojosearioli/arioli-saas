<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->nullableMorphs('contractable');
            $table->foreignId('contract_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('type');
            $table->longText('content');
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
