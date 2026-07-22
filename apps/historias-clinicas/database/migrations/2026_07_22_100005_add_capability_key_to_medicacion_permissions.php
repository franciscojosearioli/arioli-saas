<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')
            ->whereIn('title', [
                'medicacion_management_access',
                'medicacion_access',
                'medicacion_create',
                'medicacion_show',
                'medicacion_edit',
                'medicacion_delete',
            ])
            ->update(['capability_key' => 'medicacion']);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('title', [
                'medicacion_management_access',
                'medicacion_access',
                'medicacion_create',
                'medicacion_show',
                'medicacion_edit',
                'medicacion_delete',
            ])
            ->update(['capability_key' => null]);
    }
};
