<?php

namespace Database\Factories;

use App\Models\Propiedad;
use App\Models\Publicacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publicacion>
 */
class PublicacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'propiedad_id' => Propiedad::factory(),
            'destacada' => false,
        ];
    }
}
