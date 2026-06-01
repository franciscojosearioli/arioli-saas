<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Starter',
            'price' => 19,
            'max_users' => 5,
            'max_branches' => 1,
        ]);

        Plan::create([
            'name' => 'Professional',
            'price' => 49,
            'max_users' => 25,
            'max_branches' => 5,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'price' => 199,
            'max_users' => null,
            'max_branches' => null,
        ]);
    }
}