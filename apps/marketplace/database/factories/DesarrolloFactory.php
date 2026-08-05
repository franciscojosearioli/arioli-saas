<?php

namespace Database\Factories;

use App\Models\Desarrollo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Desarrollo>
 */
class DesarrolloFactory extends Factory
{
    public function definition(): array
    {
        $nombre = $this->faker->company().' '.$this->faker->randomElement(['Loteo', 'Barrio', 'Torres', 'Residencial']);

        return [
            'tenant_id' => $this->faker->unique()->slug(2),
            'desarrollo_id' => $this->faker->unique()->numberBetween(1, 100000),
            'slug' => Str::slug($nombre).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'nombre' => $nombre,
            'tipo' => $this->faker->randomElement(['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento']),
            'descripcion' => $this->faker->sentence(),
            'provincia' => $this->faker->randomElement(['Córdoba', 'Buenos Aires', 'Santa Fe']),
            'ciudad' => $this->faker->city(),
        ];
    }
}
