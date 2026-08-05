<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // hestiacp|plesk|cpanel|directadmin...
            $table->string('remote_username');
            $table->string('panel_url')->nullable();
            $table->string('status')->default('activo');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('credential_claimed_at')->nullable();
            $table->timestamps();

            $table->index(['hosting_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_accounts');
    }
};
