<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->foreignId('hosting_plan_id')->nullable()->after('client_id')
                ->constrained('hosting_plans')->nullOnDelete();
            $table->timestamp('provisioned_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('hostings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hosting_plan_id');
            $table->dropColumn('provisioned_at');
        });
    }
};
