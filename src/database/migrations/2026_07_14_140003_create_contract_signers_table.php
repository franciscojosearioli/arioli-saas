<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->string('name');
            $table->string('email');
            $table->unsignedTinyInteger('order')->default(0);
            $table->string('status')->default('pending');
            $table->string('signing_token', 64)->nullable()->unique();
            $table->timestamp('signing_token_expires_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_signers');
    }
};
