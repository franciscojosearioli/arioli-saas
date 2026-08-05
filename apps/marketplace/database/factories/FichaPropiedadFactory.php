<?php

namespace Database\Factories;

use App\Models\FichaPropiedad;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FichaPropiedad>
 */
class FichaPropiedadFactory extends Factory
{
    public function definition(): array
    {
        $titulo = ucfirst($this->faker->words(3, true));

        return [
            'tenant_id' => $this->faker->slug(2),
            'propiedad_id' => $this->faker->unique()->numberBetween(1, 100000),
            'slug' => Str::slug($titulo).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'titulo' => $titulo,
            'descripcion' => $this->faker->paragraph(),
            'precio' => $this->faker->randomFloat(2, 15000, 500000),
            'moneda' => $this->faker->randomElement(['ARS', 'USD']),
            'tipo_operacion' => $this->faker->randomElement(['venta', 'alquiler', 'reserva']),
            'tipo_propiedad' => $this->faker->randomElement(['casa', 'departamento', 'loteo']),
            'estado' => 'disponible',
            'ciudad' => $this->faker->city(),
            'provincia' => $this->faker->randomElement(['Córdoba', 'Buenos Aires', 'Santa Fe']),
            'servicios' => [],
            'caracteristicas_destacadas' => [],
            'galeria' => [],
            'publicada_en' => now(),
        ];
    }
}
