<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // new_order, new_tenant, license_expiring, license_cancelled, license_renewed
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('blue'); // blue, green, red, yellow
            $table->boolean('read')->default(false);
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};