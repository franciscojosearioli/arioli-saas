<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Crm\Leads;
use App\Models\Lead;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeadsTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_agente_que_crea_un_lead_queda_asignado_a_si_mismo(): void
    {
        $agente = $this->usuario('agente');
        $this->actingAs($agente);

        Livewire::test(Leads::class)
            ->call('nuevo')
            ->set('nombre', 'Juan Interesado')
            ->set('origen', 'whatsapp')
            ->call('guardar')
            ->assertHasNoErrors();

        $lead = Lead::where('nombre', 'Juan Interesado')->firstOrFail();
        $this->assertSame($agente->id, $lead->agente_id);
    }

    public function test_un_agente_solo_ve_sus_propios_leads_en_el_listado(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        Lead::factory()->create(['agente_id' => $agente->id, 'nombre' => 'Lead Propio']);
        Lead::factory()->create(['agente_id' => $otroAgente->id, 'nombre' => 'Lead Ajeno']);

        $this->actingAs($agente);

        Livewire::test(Leads::class)
            ->assertSee('Lead Propio')
            ->assertDontSee('Lead Ajeno');
    }

    public function test_un_agente_no_puede_editar_el_lead_de_otro_agente(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        $ajeno = Lead::factory()->create(['agente_id' => $otroAgente->id]);

        $this->actingAs($agente);

        Livewire::test(Leads::class)
            ->call('editar', $ajeno->id)
            ->assertForbidden();
    }
}
