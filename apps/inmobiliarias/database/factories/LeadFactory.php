<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agente_id' => null,
            'cliente_id' => null,
            'propiedad_id' => null,
            'nombre' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'origen' => $this->faker->randomElement(['marketplace', 'formulario', 'whatsapp', 'referido', 'otro']),
            'estado' => 'nuevo',
            'interes' => [
                'tipo' => $this->faker->randomElement(['casa', 'departamento', 'loteo']),
                'zona' => $this->faker->city(),
                'presupuesto_max' => $this->faker->numberBetween(20000, 300000),
            ],
        ];
    }
}
