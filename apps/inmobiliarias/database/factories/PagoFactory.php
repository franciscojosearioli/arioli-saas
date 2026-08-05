<?php

namespace Database\Factories;

use App\Models\Cuota;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pago>
 */
class PagoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cuota_id' => Cuota::factory(),
            'registrado_por_id' => null,
            'monto' => $this->faker->randomFloat(2, 5000, 100000),
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'medio_pago' => $this->faker->randomElement(['efectivo', 'transferencia', 'cheque', 'tarjeta', 'otro']),
        ];
    }

    public function deCuota(Cuota $cuota): static
    {
        return $this->state(fn () => ['cuota_id' => $cuota->id]);
    }
}
