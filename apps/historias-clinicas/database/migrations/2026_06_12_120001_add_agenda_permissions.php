<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAgendaPermissions extends Migration
{
    public function up()
    {
        $permissions = [
            ['title' => 'agenda_access',  'created_at' => now(), 'updated_at' => now()],
            ['title' => 'agenda_create',  'created_at' => now(), 'updated_at' => now()],
            ['title' => 'agenda_show',    'created_at' => now(), 'updated_at' => now()],
            ['title' => 'agenda_edit',    'created_at' => now(), 'updated_at' => now()],
            ['title' => 'agenda_delete',  'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('title', $permission['title'])->exists();
            if (! $exists) {
                DB::table('permissions')->insert($permission);
            }
        }

        // Assign all agenda permissions to role id=1 (admin) if that role exists
        $adminRoleId = 1;
        if (DB::table('roles')->where('id', $adminRoleId)->exists()) {
            $permissionIds = DB::table('permissions')
                ->whereIn('title', ['agenda_access', 'agenda_create', 'agenda_show', 'agenda_edit', 'agenda_delete'])
                ->pluck('id');

            foreach ($permissionIds as $permId) {
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

    public function down()
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('title', ['agenda_access', 'agenda_create', 'agenda_show', 'agenda_edit', 'agenda_delete'])
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')
            ->whereIn('title', ['agenda_access', 'agenda_create', 'agenda_show', 'agenda_edit', 'agenda_delete'])
            ->delete();
    }
}
