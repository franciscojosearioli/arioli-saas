<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Desarrollo;
use App\Models\Propiedad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Propiedad>
 */
class PropiedadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = $this->faker->randomElement([
            'loteo', 'casa', 'departamento', 'local', 'oficina', 'galpon', 'campo', 'cochera',
        ]);

        return [
            'desarrollo_id' => null,
            'propietario_id' => null,
            'tipo' => $tipo,
            'titulo' => ucfirst($tipo).' en '.$this->faker->streetName(),
            'descripcion' => $this->faker->optional()->paragraph(),
            'estado' => 'disponible',
            'precio' => $this->faker->randomFloat(2, 15000, 500000),
            'moneda' => $this->faker->randomElement(['ARS', 'USD']),
            'superficie_cubierta' => $tipo === 'loteo' ? null : $this->faker->randomFloat(2, 30, 400),
            'superficie_total' => $this->faker->randomFloat(2, 100, 1000),
            'ambientes' => in_array($tipo, ['casa', 'departamento'], true) ? $this->faker->numberBetween(1, 6) : null,
            'dormitorios' => in_array($tipo, ['casa', 'departamento'], true) ? $this->faker->numberBetween(1, 4) : null,
            'banos' => in_array($tipo, ['casa', 'departamento', 'local', 'oficina'], true) ? $this->faker->numberBetween(1, 3) : null,
            'cocheras' => $this->faker->numberBetween(0, 2),
            'direccion' => $this->faker->streetAddress(),
            'provincia' => 'Córdoba',
            'ciudad' => $this->faker->city(),
            'barrio' => $this->faker->optional()->streetName(),
            'servicios' => $this->faker->randomElements(
                ['agua', 'luz', 'gas', 'cloacas', 'internet'],
                $this->faker->numberBetween(0, 5)
            ),
        ];
    }

    public function deDesarrollo(?Desarrollo $desarrollo = null, ?string $manzana = null, ?string $numeroLote = null): static
    {
        return $this->state(fn () => [
            'desarrollo_id' => ($desarrollo ?? Desarrollo::factory()->create())->id,
            'tipo' => 'loteo',
            'manzana' => $manzana ?? (string) $this->faker->numberBetween(1, 20),
            'numero_lote' => $numeroLote ?? (string) $this->faker->unique()->numberBetween(1, 999),
        ]);
    }

    public function conPropietario(?Cliente $cliente = null): static
    {
        return $this->state(fn () => [
            'propietario_id' => ($cliente ?? Cliente::factory()->create())->id,
        ]);
    }
}
