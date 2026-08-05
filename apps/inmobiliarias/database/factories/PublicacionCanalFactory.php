<?php

namespace Database\Factories;

use App\Models\Publicacion;
use App\Models\PublicacionCanal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PublicacionCanal>
 */
class PublicacionCanalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'publicacion_id' => Publicacion::factory(),
            'canal' => 'sitio_web',
            'estado' => 'borrador',
        ];
    }

    public function publicada(?string $externalId = null): static
    {
        return $this->state(fn () => [
            'estado' => 'publicada',
            'external_id' => $externalId ?? (string) $this->faker->uuid(),
            'fecha_publicada' => now(),
        ]);
    }
}
