<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('ssl_certificate_id')->nullable()->after('hosting_id')->constrained('ssl_certificates')->nullOnDelete();
            $table->foreignId('cloudflare_service_id')->nullable()->after('ssl_certificate_id')->constrained('cloudflare_services')->nullOnDelete();
            $table->foreignId('license_id')->nullable()->after('cloudflare_service_id')->constrained('licenses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ssl_certificate_id');
            $table->dropConstrainedForeignId('cloudflare_service_id');
            $table->dropConstrainedForeignId('license_id');
        });
    }
};
