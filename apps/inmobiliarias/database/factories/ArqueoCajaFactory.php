<?php

namespace Database\Factories;

use App\Models\ArqueoCaja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArqueoCaja>
 */
class ArqueoCajaFactory extends Factory
{
    public function definition(): array
    {
        $esperado = $this->faker->randomFloat(2, 10000, 200000);

        return [
            'fecha' => $this->faker->unique()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'monto_esperado' => $esperado,
            'monto_contado' => $esperado,
        ];
    }
}
