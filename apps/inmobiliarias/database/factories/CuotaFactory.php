<?php

namespace Database\Factories;

use App\Models\Cuota;
use App\Models\Operacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cuota>
 */
class CuotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'operacion_id' => Operacion::factory(),
            'numero' => 1,
            'fecha_vencimiento' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'monto' => $this->faker->randomFloat(2, 5000, 100000),
            'moneda' => 'ARS',
            'estado' => 'pendiente',
        ];
    }

    public function deOperacion(Operacion $operacion): static
    {
        return $this->state(fn () => ['operacion_id' => $operacion->id]);
    }
}
