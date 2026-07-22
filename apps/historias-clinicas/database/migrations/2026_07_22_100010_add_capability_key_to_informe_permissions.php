<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')
            ->where('title', 'like', 'informe%')
            ->update(['capability_key' => 'historia_clinica']);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('title', 'like', 'informe%')
            ->where('capability_key', 'historia_clinica')
            ->update(['capability_key' => null]);
    }
};
