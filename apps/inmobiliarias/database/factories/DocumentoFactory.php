<?php

namespace Database\Factories;

use App\Models\Documento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Documento>
 */
class DocumentoFactory extends Factory
{
    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['boleto', 'escritura', 'dni', 'comprobante', 'contrato', 'otro']);

        return [
            'tipo' => $tipo,
            'nombre' => ucfirst($tipo).' - '.$this->faker->word(),
            'archivo' => 'documentos/'.$this->faker->uuid().'.pdf',
            'fecha_vencimiento' => $this->faker->optional()->dateTimeBetween('now', '+2 years')?->format('Y-m-d'),
            'version' => 1,
        ];
    }
}
