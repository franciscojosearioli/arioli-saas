<?php

namespace Tests\Feature;

use App\Models\FichaPropiedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationsApiTest extends TestCase
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

    public function test_sin_token_no_se_puede_publicar(): void
    {
        $this->postJson('/api/publications', [])->assertUnauthorized();
    }

    public function test_con_token_invalido_no_se_puede_publicar(): void
    {
        $this->withHeader('Authorization', 'Bearer token-equivocado')
            ->postJson('/api/publications', [])
            ->assertUnauthorized();
    }

    public function test_publicar_una_ficha_nueva(): void
    {
        $respuesta = $this->conToken()->postJson('/api/publications', [
            'tenant_id' => 'demo',
            'propiedad_id' => 42,
            'titulo' => 'Casa 3 dormitorios en Nueva Córdoba',
            'moneda' => 'ARS',
            'tipo_propiedad' => 'casa',
            'estado' => 'disponible',
        ])->assertCreated();

        $respuesta->assertJsonStructure(['id', 'slug']);
        $this->assertDatabaseHas('fichas_propiedad', [
            'tenant_id' => 'demo',
            'propiedad_id' => 42,
            'titulo' => 'Casa 3 dormitorios en Nueva Córdoba',
        ]);
    }

    public function test_publicar_dos_veces_la_misma_propiedad_del_mismo_tenant_actualiza_en_vez_de_duplicar(): void
    {
        $this->conToken()->postJson('/api/publications', [
            'tenant_id' => 'demo', 'propiedad_id' => 42, 'titulo' => 'Casa original',
            'moneda' => 'ARS', 'tipo_propiedad' => 'casa', 'estado' => 'disponible',
        ])->assertCreated();

        $this->conToken()->postJson('/api/publications', [
            'tenant_id' => 'demo', 'propiedad_id' => 42, 'titulo' => 'Casa renombrada',
            'moneda' => 'ARS', 'tipo_propiedad' => 'casa', 'estado' => 'disponible',
        ])->assertCreated();

        $this->assertSame(1, FichaPropiedad::where('tenant_id', 'demo')->where('propiedad_id', 42)->count());
        $this->assertDatabaseHas('fichas_propiedad', ['titulo' => 'Casa renombrada']);
    }

    public function test_actualizar_una_ficha_existente(): void
    {
        $ficha = FichaPropiedad::factory()->create(['precio' => '100000.00']);

        $this->conToken()
            ->putJson("/api/publications/{$ficha->id}", ['precio' => '120000.00'])
            ->assertOk();

        $this->assertSame('120000.00', $ficha->fresh()->precio);
    }

    public function test_despublicar_una_ficha_la_elimina(): void
    {
        $ficha = FichaPropiedad::factory()->create();

        $this->conToken()->deleteJson("/api/publications/{$ficha->id}")->assertNoContent();

        $this->assertDatabaseMissing('fichas_propiedad', ['id' => $ficha->id]);
    }
}
