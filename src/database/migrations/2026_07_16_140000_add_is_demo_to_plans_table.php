<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false)->after('is_perpetual');
        });

        // Los planes Demo existentes se identificaban solo por price=0 — con licencias
        // indefinidas sin costo, price=0 deja de alcanzar para distinguirlos. Se marcan
        // explícitamente acá para no depender nunca más de esa heurística.
        DB::table('plans')->where('name', 'Demo')->update(['is_demo' => true]);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('is_demo');
        });
    }
};
