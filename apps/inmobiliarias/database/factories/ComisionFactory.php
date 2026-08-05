<?php

namespace Database\Factories;

use App\Models\Comision;
use App\Models\Operacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comision>
 */
class ComisionFactory extends Factory
{
    public function definition(): array
    {
        $porcentaje = $this->faker->randomFloat(2, 2, 6);
        $monto = $this->faker->randomFloat(2, 500, 20000);

        return [
            'operacion_id' => Operacion::factory(),
            'agente_id' => User::factory(),
            'porcentaje' => $porcentaje,
            'monto' => $monto,
            'moneda' => 'ARS',
            'estado' => 'pendiente',
        ];
    }
}
