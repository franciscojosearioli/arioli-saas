<?php

namespace Database\Factories;

use App\Models\PerfilConstructora;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PerfilConstructora>
 */
class PerfilConstructoraFactory extends Factory
{
    public function definition(): array
    {
        $nombre = $this->faker->company();

        return [
            'tenant_id' => $this->faker->unique()->slug(2),
            'constructora_id' => $this->faker->unique()->numberBetween(1, 100000),
            'slug' => Str::slug($nombre).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'nombre' => $nombre,
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
