<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogo\Constructoras;
use App\Models\Constructora;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConstructorasTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_admin_crea_una_constructora(): void
    {
        $this->actingAs($this->usuario('admin'));

        Livewire::test(Constructoras::class)
            ->set('nombre', 'Edisur')
            ->set('email', 'contacto@edisur.com.ar')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('constructoras', ['nombre' => 'Edisur']);
    }

    public function test_agente_no_puede_crear_una_constructora(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Constructoras::class)
            ->set('nombre', 'Edisur')
            ->call('guardar')
            ->assertForbidden();
    }

    public function test_el_listado_filtra_por_nombre(): void
    {
        $this->actingAs($this->usuario('admin'));

        Constructora::factory()->create(['nombre' => 'Edisur']);
        Constructora::factory()->create(['nombre' => 'Grupo Nazur']);

        Livewire::test(Constructoras::class)
            ->set('search', 'Edisur')
            ->assertSee('Edisur')
            ->assertDontSee('Grupo Nazur');
    }
}
