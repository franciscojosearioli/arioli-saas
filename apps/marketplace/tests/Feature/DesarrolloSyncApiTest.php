<?php

namespace Tests\Feature;

use App\Models\Desarrollo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesarrolloSyncApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['marketplace.api_token' => 'token-de-test']);
    }

    private function conToken(): static
    {
        return $this->withHeader('Authorization', 'Bearer token-de-test');
    }

    public function test_sincronizar_un_desarrollo(): void
    {
        $this->conToken()->putJson('/api/desarrollos', [
            'tenant_id' => 'demo',
            'desarrollo_id' => 5,
            'nombre' => 'Loteo Las Acacias',
            'tipo' => 'loteo',
            'provincia' => 'Córdoba',
            'ciudad' => 'Córdoba',
        ])->assertOk()->assertJsonStructure(['id', 'slug']);

        $this->assertDatabaseHas('desarrollos', [
            'tenant_id' => 'demo',
            'desarrollo_id' => 5,
            'nombre' => 'Loteo Las Acacias',
            'tipo' => 'loteo',
        ]);
    }

    public function test_sincronizar_dos_veces_el_mismo_desarrollo_actualiza_en_vez_de_duplicar(): void
    {
        $this->conToken()->putJson('/api/desarrollos', [
            'tenant_id' => 'demo', 'desarrollo_id' => 5, 'nombre' => 'Nombre original', 'tipo' => 'loteo',
        ])->assertOk();

        $this->conToken()->putJson('/api/desarrollos', [
            'tenant_id' => 'demo', 'desarrollo_id' => 5, 'nombre' => 'Nombre actualizado', 'tipo' => 'loteo',
        ])->assertOk();

        $this->assertSame(1, Desarrollo::where('tenant_id', 'demo')->where('desarrollo_id', 5)->count());
        $this->assertDatabaseHas('desarrollos', ['nombre' => 'Nombre actualizado']);
    }

    public function test_rechaza_un_wkt_de_ubicacion_invalido(): void
    {
        $this->conToken()->putJson('/api/desarrollos', [
            'tenant_id' => 'demo', 'desarrollo_id' => 5, 'nombre' => 'X', 'tipo' => 'loteo',
            'ubicacion_wkt' => 'DROP TABLE desarrollos;',
        ])->assertUnprocessable();
    }

    public function test_sin_token_no_se_puede_sincronizar(): void
    {
        $this->putJson('/api/desarrollos', [
            'tenant_id' => 'demo', 'desarrollo_id' => 5, 'nombre' => 'X', 'tipo' => 'loteo',
        ])->assertUnauthorized();
    }
}
