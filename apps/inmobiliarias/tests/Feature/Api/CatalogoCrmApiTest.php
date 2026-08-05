<?php

namespace Tests\Feature\Api;

use App\Models\Cliente;
use App\Models\Desarrollo;
use App\Models\Lead;
use App\Models\Propiedad;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogoCrmApiTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_usuario_sin_autenticar_no_accede_a_la_api(): void
    {
        $this->getJson('/api/v1/propiedades')->assertUnauthorized();
    }

    public function test_admin_gestiona_el_catalogo_completo(): void
    {
        Sanctum::actingAs($this->usuario('admin'));

        $constructora = $this->postJson('/api/v1/constructoras', [
            'nombre' => 'Edisur',
        ])->assertCreated()->json('data');

        $desarrollo = $this->postJson('/api/v1/desarrollos', [
            'constructora_id' => $constructora['id'],
            'nombre' => 'Barrio Los Robles',
            'tipo' => 'loteo',
        ])->assertCreated()->json('data');

        $propiedad = $this->postJson('/api/v1/propiedades', [
            'desarrollo_id' => $desarrollo['id'],
            'tipo' => 'loteo',
            'titulo' => 'Lote 12, Manzana 5',
            'manzana' => '5',
            'numero_lote' => '12',
        ])->assertCreated()->json('data');

        $this->assertSame('disponible', $propiedad['estado']);

        $this->patchJson("/api/v1/propiedades/{$propiedad['id']}", ['estado' => 'reservado'])
            ->assertOk()
            ->assertJsonPath('data.estado', 'reservado');

        $this->deleteJson("/api/v1/desarrollos/{$desarrollo['id']}")->assertNoContent();
    }

    public function test_agente_no_puede_gestionar_constructoras_ni_desarrollos(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $this->postJson('/api/v1/constructoras', ['nombre' => 'Edisur'])->assertForbidden();

        $desarrollo = Desarrollo::factory()->create();
        $this->putJson("/api/v1/desarrollos/{$desarrollo->id}", ['nombre' => 'Otro nombre'])
            ->assertForbidden();
    }

    public function test_agente_carga_propiedades_y_clientes_del_equipo(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $this->postJson('/api/v1/propiedades', [
            'tipo' => 'casa',
            'titulo' => 'Casa 3 dormitorios en Nueva Córdoba',
        ])->assertCreated();

        $cliente = $this->postJson('/api/v1/clientes', [
            'tipo_persona' => 'fisica',
            'nombre' => 'Ana Propietaria',
        ])->assertCreated()->json('data');

        $this->assertSame('fisica', $cliente['tipo_persona']);
    }

    public function test_solo_lectura_lee_pero_no_escribe(): void
    {
        Sanctum::actingAs($this->usuario('solo-lectura'));

        $propiedad = Propiedad::factory()->create();

        $this->getJson('/api/v1/propiedades')->assertOk();
        $this->getJson("/api/v1/propiedades/{$propiedad->id}")->assertOk();
        $this->postJson('/api/v1/propiedades', ['tipo' => 'casa', 'titulo' => 'x'])->assertForbidden();
    }

    public function test_un_agente_solo_ve_sus_propios_leads_en_el_listado(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        $propio = Lead::factory()->create(['agente_id' => $agente->id]);
        Lead::factory()->create(['agente_id' => $otroAgente->id]);

        Sanctum::actingAs($agente);

        $response = $this->getJson('/api/v1/leads')->assertOk();

        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($propio->id));
        $this->assertCount(1, $ids);
    }

    public function test_un_agente_no_puede_ver_ni_editar_el_lead_de_otro_agente(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        $ajeno = Lead::factory()->create(['agente_id' => $otroAgente->id]);

        Sanctum::actingAs($agente);

        $this->getJson("/api/v1/leads/{$ajeno->id}")->assertForbidden();
        $this->patchJson("/api/v1/leads/{$ajeno->id}", ['nombre' => 'Otro nombre'])->assertForbidden();
    }

    public function test_crear_una_propiedad_valida_tipo_y_titulo_requeridos(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $this->postJson('/api/v1/propiedades', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tipo', 'titulo']);
    }

    public function test_un_cliente_muestra_sus_propiedades_relacionadas_con_relaciones_cargadas(): void
    {
        Sanctum::actingAs($this->usuario('admin'));

        $cliente = Cliente::factory()->create();
        Propiedad::factory()->count(2)->conPropietario($cliente)->create();

        $this->getJson("/api/v1/clientes/{$cliente->id}")
            ->assertOk()
            ->assertJsonPath('data.propiedades_count', 2);
    }

    public function test_el_listado_de_propiedades_filtra_por_estado(): void
    {
        Sanctum::actingAs($this->usuario('admin'));

        Propiedad::factory()->create(['estado' => 'disponible']);
        Propiedad::factory()->create(['estado' => 'vendido']);

        $response = $this->getJson('/api/v1/propiedades?estado=vendido')->assertOk();

        $estados = collect($response->json('data'))->pluck('estado')->unique();

        $this->assertSame(['vendido'], $estados->values()->all());
    }
}
