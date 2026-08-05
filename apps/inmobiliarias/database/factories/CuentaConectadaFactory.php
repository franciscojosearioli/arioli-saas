<?php

namespace Database\Factories;

use App\Models\CuentaConectada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CuentaConectada>
 */
class CuentaConectadaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'canal' => 'facebook',
            'external_account_id' => (string) $this->faker->numberBetween(100000000000000, 999999999999999),
            'external_account_name' => $this->faker->company(),
            'access_token' => $this->faker->sha256(),
            'scopes' => ['pages_show_list', 'pages_read_engagement', 'pages_manage_posts'],
            'token_expira_en' => now()->addDays(60),
            'estado' => 'activa',
        ];
    }

    public function instagram(): static
    {
        return $this->state(fn () => [
            'canal' => 'instagram',
            'scopes' => ['instagram_basic', 'instagram_content_publish'],
        ]);
    }

    public function requiereReconexion(): static
    {
        return $this->state(fn () => [
            'estado' => 'requiere_reconexion',
            'ultimo_error' => 'El token expiró.',
        ]);
    }
}
