<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // VARCHAR instead of ENUM: future types (commercial/demo/internal/trial/lifetime)
            // without requiring additional migrations
            $table->string('license_type', 20)->default('commercial')->after('active');

            // Allow NULL for demo licenses (no expiration)
            $table->date('expires_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn('license_type');
            $table->date('expires_at')->nullable(false)->change();
        });
    }
};
