<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_template_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('content');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_template_versions');
    }
};
