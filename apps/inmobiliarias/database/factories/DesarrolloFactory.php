<?php

namespace Database\Factories;

use App\Models\Constructora;
use App\Models\Desarrollo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Desarrollo>
 */
class DesarrolloFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'constructora_id' => null,
            'nombre' => 'Desarrollo '.$this->faker->streetName(),
            'tipo' => $this->faker->randomElement(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento']),
            'descripcion' => $this->faker->optional()->paragraph(),
            'provincia' => $this->faker->randomElement(config('argentina.provincias')),
            'ciudad' => $this->faker->city(),
            'barrio' => $this->faker->optional()->streetName(),
        ];
    }

    public function deConstructora(?Constructora $constructora = null): static
    {
        return $this->state(fn () => [
            'constructora_id' => ($constructora ?? Constructora::factory()->create())->id,
        ]);
    }
}
