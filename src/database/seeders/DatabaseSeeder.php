<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            PlanSeeder::class,
            ProductSeeder::class,
        ]);

        // Admin central
        $admin = User::factory()->admin()->create([
            'name'  => 'Francisco Arioli',
            'email' => 'francisco@arioli.dev',
        ]);
        $admin->assignRole('admin_central');
    }
}