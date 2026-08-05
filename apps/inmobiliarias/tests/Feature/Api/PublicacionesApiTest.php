<?php

namespace Tests\Feature\Api;

use App\Models\Propiedad;
use App\Models\Publicacion;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicacionesApiTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_agente_publica_una_propiedad_y_activa_el_canal_sitio_web(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $propiedad = Propiedad::factory()->create();

        $publicacion = $this->postJson("/api/v1/propiedades/{$propiedad->id}/publicacion")
            ->assertCreated()
            ->json('data');

        $this->postJson("/api/v1/publicaciones/{$publicacion['id']}/canales", ['canal' => 'sitio_web'])
            ->assertOk()
            ->assertJsonPath('data.canales.0.canal', 'sitio_web')
            ->assertJsonPath('data.canales.0.estado', 'borrador');
    }

    public function test_solo_lectura_no_puede_publicar_una_propiedad(): void
    {
        Sanctum::actingAs($this->usuario('solo-lectura'));

        $propiedad = Propiedad::factory()->create();

        $this->postJson("/api/v1/propiedades/{$propiedad->id}/publicacion")->assertForbidden();
    }

    public function test_pausar_y_reintentar_un_canal(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $publicacion = Publicacion::factory()->create();
        $canal = $publicacion->activarCanal('sitio_web');

        $this->postJson("/api/v1/publicacion-canales/{$canal->id}/pausar")
            ->assertOk()
            ->assertJsonPath('data.estado', 'pausada');

        $this->postJson("/api/v1/publicacion-canales/{$canal->id}/reintentar")
            ->assertOk()
            ->assertJsonPath('data.estado', 'borrador');
    }

    public function test_marcar_una_publicacion_como_destacada(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $publicacion = Publicacion::factory()->create();

        $this->patchJson("/api/v1/publicaciones/{$publicacion->id}", ['destacada' => true])
            ->assertOk()
            ->assertJsonPath('data.destacada', true);
    }
}
