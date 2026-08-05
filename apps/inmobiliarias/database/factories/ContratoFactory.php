<?php

namespace Database\Factories;

use App\Models\Contrato;
use App\Models\Operacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contrato>
 */
class ContratoFactory extends Factory
{
    public function definition(): array
    {
        $inicio = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'operacion_id' => Operacion::factory(),
            'estado' => 'borrador',
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'fecha_fin' => $this->faker->optional()->dateTimeBetween($inicio, '+2 years')?->format('Y-m-d'),
            'clausulas' => $this->faker->optional()->paragraphs(3, true),
        ];
    }

    public function deOperacion(Operacion $operacion): static
    {
        return $this->state(fn () => ['operacion_id' => $operacion->id]);
    }
}
