<?php

namespace Database\Factories;

use App\Models\Operacion;
use App\Models\Propiedad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operacion>
 */
class OperacionFactory extends Factory
{
    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['venta', 'alquiler', 'reserva']);

        return [
            'propiedad_id' => Propiedad::factory(),
            'agente_id' => null,
            'tipo' => $tipo,
            'estado' => 'abierta',
            'fecha_inicio' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'monto' => $this->faker->randomFloat(2, 15000, 500000),
            'moneda' => $this->faker->randomElement(['ARS', 'USD']),
            'indice_actualizacion' => $tipo === 'alquiler' ? $this->faker->randomElement(['ICL', 'IPC']) : null,
        ];
    }

    public function deTipo(string $tipo): static
    {
        return $this->state(fn () => ['tipo' => $tipo]);
    }

    public function dePropiedad(Propiedad $propiedad): static
    {
        return $this->state(fn () => ['propiedad_id' => $propiedad->id]);
    }
}
