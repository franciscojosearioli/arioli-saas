<?php

namespace Database\Factories;

use App\Models\FotoPropiedad;
use App\Models\Propiedad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FotoPropiedad>
 */
class FotoPropiedadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'propiedad_id' => Propiedad::factory(),
            'url' => 'propiedades/'.$this->faker->uuid().'.jpg',
            'orden' => 0,
            'es_principal' => false,
        ];
    }
}
