<?php

namespace Tests\Feature;

use App\Models\Desarrollo;
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

    public function test_publicar_una_ficha_vincula_el_desarrollo_ya_sincronizado(): void
    {
        $desarrollo = Desarrollo::factory()->create(['tenant_id' => 'demo', 'desarrollo_id' => 5]);

        $respuesta = $this->conToken()->postJson('/api/publications', [
            'tenant_id' => 'demo', 'propiedad_id' => 42, 'titulo' => 'Lote 12',
            'moneda' => 'ARS', 'tipo_propiedad' => 'loteo', 'estado' => 'disponible',
            'desarrollo_id' => 5,
        ])->assertCreated();

        $ficha = FichaPropiedad::find($respuesta->json('id'));
        $this->assertSame($desarrollo->id, $ficha->desarrollo_id);
    }

    public function test_publicar_una_ficha_sin_desarrollo_sincronizado_todavia_no_falla(): void
    {
        $this->conToken()->postJson('/api/publications', [
            'tenant_id' => 'demo', 'propiedad_id' => 42, 'titulo' => 'Lote 12',
            'moneda' => 'ARS', 'tipo_propiedad' => 'loteo', 'estado' => 'disponible',
            'desarrollo_id' => 999,
        ])->assertCreated();

        $this->assertDatabaseHas('fichas_propiedad', ['propiedad_id' => 42, 'desarrollo_id' => null]);
    }

    public function test_despublicar_una_ficha_la_elimina(): void
    {
        $ficha = FichaPropiedad::factory()->create();

        $this->conToken()->deleteJson("/api/publications/{$ficha->id}")->assertNoContent();

        $this->assertDatabaseMissing('fichas_propiedad', ['id' => $ficha->id]);
    }
}
