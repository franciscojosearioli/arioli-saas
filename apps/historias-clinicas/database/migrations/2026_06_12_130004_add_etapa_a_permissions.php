<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'especialidad_access',
            'especialidad_create',
            'especialidad_edit',
            'especialidad_delete',
        ];

        foreach ($permissions as $title) {
            if (! DB::table('permissions')->where('title', $title)->exists()) {
                DB::table('permissions')->insert([
                    'title'      => $title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $adminRoleId = 1;
        if (DB::table('roles')->where('id', $adminRoleId)->exists()) {
            $ids = DB::table('permissions')->whereIn('title', $permissions)->pluck('id');

            foreach ($ids as $permId) {
                $exists = DB::table('permission_role')
                    ->where('permission_id', $permId)
                    ->where('role_id', $adminRoleId)
                    ->exists();
                if (! $exists) {
                    DB::table('permission_role')->insert([
                        'permission_id' => $permId,
                        'role_id'       => $adminRoleId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $permissions = [
            'especialidad_access',
            'especialidad_create',
            'especialidad_edit',
            'especialidad_delete',
        ];

        $ids = DB::table('permissions')->whereIn('title', $permissions)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('title', $permissions)->delete();
    }
};
