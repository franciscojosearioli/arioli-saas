<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // No-op: plans are created via ProductSeeder or admin panel.
        // The old Starter/Professional/Enterprise plans (no product_id) are intentionally omitted.
    }
}
