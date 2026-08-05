<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Crm\Clientes;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientesTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_agente_crea_un_cliente(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Clientes::class)
            ->set('tipo_persona', 'juridica')
            ->set('nombre', 'Constructora Ana SA')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clientes', ['nombre' => 'Constructora Ana SA', 'tipo_persona' => 'juridica']);
    }

    public function test_solo_lectura_no_puede_crear_un_cliente(): void
    {
        $this->actingAs($this->usuario('solo-lectura'));

        Livewire::test(Clientes::class)
            ->set('nombre', 'Ana')
            ->call('guardar')
            ->assertForbidden();
    }
}
