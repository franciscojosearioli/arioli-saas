<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'tipo_persona' => 'fisica',
            'nombre' => $this->faker->name(),
            'documento' => $this->faker->numerify('########'),
            'email' => $this->faker->optional()->safeEmail(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'direccion' => $this->faker->optional()->streetAddress(),
            'provincia' => 'Córdoba',
            'ciudad' => $this->faker->city(),
        ];
    }
}
