<?php

namespace Database\Factories;

use App\Models\Constructora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Constructora>
 */
class ConstructoraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company().' Construcciones',
            'descripcion' => $this->faker->optional()->paragraph(),
            'email' => $this->faker->optional()->companyEmail(),
            'telefono' => $this->faker->optional()->phoneNumber(),
        ];
    }
}
