<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('title');
            $table->longText('payment_terms')->nullable()->after('content');
            $table->string('timeline_estimate')->nullable()->after('payment_terms');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['content', 'payment_terms', 'timeline_estimate']);
        });
    }
};
