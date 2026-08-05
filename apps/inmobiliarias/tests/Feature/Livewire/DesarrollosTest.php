<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogo\Desarrollos;
use App\Models\Constructora;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesarrollosTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_admin_crea_un_desarrollo(): void
    {
        $this->actingAs($this->usuario('admin'));

        $constructora = Constructora::factory()->create();

        Livewire::test(Desarrollos::class)
            ->set('constructora_id', $constructora->id)
            ->set('nombre', 'Barrio Los Robles')
            ->set('tipo', 'barrio_cerrado')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('desarrollos', [
            'nombre' => 'Barrio Los Robles',
            'constructora_id' => $constructora->id,
        ]);
    }

    public function test_agente_no_puede_crear_un_desarrollo(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Desarrollos::class)
            ->set('nombre', 'Barrio Los Robles')
            ->call('guardar')
            ->assertForbidden();
    }
}
