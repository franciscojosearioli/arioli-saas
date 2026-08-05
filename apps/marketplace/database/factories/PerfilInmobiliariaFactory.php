<?php

namespace Database\Factories;

use App\Models\PerfilInmobiliaria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PerfilInmobiliaria>
 */
class PerfilInmobiliariaFactory extends Factory
{
    public function definition(): array
    {
        $nombre = $this->faker->company();

        return [
            'tenant_id' => $this->faker->unique()->slug(2),
            'slug' => Str::slug($nombre).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'nombre_comercial' => $nombre,
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
