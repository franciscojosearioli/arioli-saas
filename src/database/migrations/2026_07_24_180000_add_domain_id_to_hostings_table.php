<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->foreignId('domain_id')->nullable()->after('client_id')->constrained('client_domains')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('domain_id');
        });
    }
};
