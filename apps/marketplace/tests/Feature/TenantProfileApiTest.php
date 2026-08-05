<?php

namespace Tests\Feature;

use App\Models\PerfilConstructora;
use App\Models\PerfilInmobiliaria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantProfileApiTest extends TestCase
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

    public function test_sincronizar_el_perfil_de_una_inmobiliaria(): void
    {
        $this->conToken()->putJson('/api/tenant-profile', [
            'tenant_id' => 'demo',
            'nombre_comercial' => 'Edisur Inmobiliaria',
            'descripcion' => 'Más de 20 años en el mercado.',
        ])->assertOk()->assertJsonStructure(['id', 'slug']);

        $this->assertDatabaseHas('perfiles_inmobiliaria', [
            'tenant_id' => 'demo',
            'nombre_comercial' => 'Edisur Inmobiliaria',
        ]);
    }

    public function test_sincronizar_el_perfil_dos_veces_actualiza_en_vez_de_duplicar(): void
    {
        $this->conToken()->putJson('/api/tenant-profile', [
            'tenant_id' => 'demo', 'nombre_comercial' => 'Nombre original',
        ])->assertOk();

        $this->conToken()->putJson('/api/tenant-profile', [
            'tenant_id' => 'demo', 'nombre_comercial' => 'Nombre actualizado',
        ])->assertOk();

        $this->assertSame(1, PerfilInmobiliaria::where('tenant_id', 'demo')->count());
        $this->assertDatabaseHas('perfiles_inmobiliaria', ['nombre_comercial' => 'Nombre actualizado']);
    }

    public function test_sincronizar_el_perfil_de_una_constructora(): void
    {
        $this->conToken()->putJson('/api/constructora-profile', [
            'tenant_id' => 'demo',
            'constructora_id' => 7,
            'nombre' => 'Edisur',
            'descripcion' => 'Desarrolladora de barrios cerrados.',
        ])->assertOk()->assertJsonStructure(['id', 'slug']);

        $this->assertDatabaseHas('perfiles_constructora', [
            'tenant_id' => 'demo',
            'constructora_id' => 7,
            'nombre' => 'Edisur',
        ]);
    }

    public function test_sin_token_no_se_puede_sincronizar_un_perfil(): void
    {
        $this->putJson('/api/tenant-profile', ['tenant_id' => 'demo', 'nombre_comercial' => 'X'])
            ->assertUnauthorized();
    }

    public function test_ver_el_perfil_publico_de_una_inmobiliaria(): void
    {
        $perfil = PerfilInmobiliaria::factory()->create(['nombre_comercial' => 'Edisur Inmobiliaria']);

        $this->get("/inmobiliarias/{$perfil->slug}")
            ->assertOk()
            ->assertSee('Edisur Inmobiliaria');
    }

    public function test_ver_el_perfil_publico_de_una_constructora(): void
    {
        $perfil = PerfilConstructora::factory()->create(['nombre' => 'Edisur']);

        $this->get("/constructoras/{$perfil->slug}")
            ->assertOk()
            ->assertSee('Edisur');
    }
}
