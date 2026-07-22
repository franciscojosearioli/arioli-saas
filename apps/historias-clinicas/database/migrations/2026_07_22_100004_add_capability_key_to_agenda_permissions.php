<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')
            ->whereIn('title', [
                'agenda_access',
                'agenda_create',
                'agenda_show',
                'agenda_edit',
                'agenda_delete',
            ])
            ->update(['capability_key' => 'agenda']);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('title', [
                'agenda_access',
                'agenda_create',
                'agenda_show',
                'agenda_edit',
                'agenda_delete',
            ])
            ->update(['capability_key' => null]);
    }
};
