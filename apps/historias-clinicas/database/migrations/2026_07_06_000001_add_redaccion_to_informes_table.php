<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            if (!Schema::hasColumn('informes', 'redaccion')) {
                $table->longText('redaccion')->nullable()->after('codigo_cie10');
            }
        });
    }

    public function down(): void
    {
        Schema::table('informes', function (Blueprint $table) {
            $table->dropColumn('redaccion');
        });
    }
};
