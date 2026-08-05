<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Configuracion;
use App\Livewire\Finanzas\Caja;
use App\Livewire\Finanzas\Cobranza;
use App\Livewire\Finanzas\Comisiones;
use App\Models\Comision;
use App\Models\Cuota;
use App\Models\Operacion;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinanzasTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_agente_no_puede_entrar_a_cobranza(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Cobranza::class)->assertForbidden();
    }

    public function test_administrativo_registra_un_pago_desde_cobranza(): void
    {
        $administrativo = $this->usuario('administrativo');
        $operacion = Operacion::factory()->create();
        $cuota = Cuota::factory()->deOperacion($operacion)->create(['monto' => '10000.00']);

        $this->actingAs($administrativo);

        Livewire::test(Cobranza::class)
            ->call('registrarPago', $cuota->id)
            ->set('fecha', '2026-09-01')
            ->set('medio_pago', 'efectivo')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertSame('pagada', $cuota->fresh()->estado);
    }

    public function test_un_agente_ve_solo_su_propia_comision_en_el_listado(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        Comision::factory()->create(['agente_id' => $agente->id]);
        Comision::factory()->create(['agente_id' => $otroAgente->id]);

        $this->actingAs($agente);

        $componente = Livewire::test(Comisiones::class);

        $this->assertCount(1, $componente->viewData('comisiones'));
    }

    public function test_administrativo_liquida_una_comision(): void
    {
        $comision = Comision::factory()->create(['estado' => 'pendiente']);

        $this->actingAs($this->usuario('administrativo'));

        Livewire::test(Comisiones::class)->call('liquidar', $comision->id);

        $this->assertSame('liquidada', $comision->fresh()->estado);
    }

    public function test_un_agente_no_puede_cerrar_caja(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Caja::class)->assertForbidden();
    }

    public function test_administrativo_cierra_la_caja_del_dia(): void
    {
        $this->actingAs($this->usuario('administrativo'));

        Livewire::test(Caja::class)
            ->call('nuevo')
            ->set('fecha', '2026-09-05')
            ->set('monto_contado', '10000.00')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('arqueos_caja', ['fecha' => '2026-09-05 00:00:00']);
    }

    public function test_solo_admin_puede_ver_configuracion(): void
    {
        $this->actingAs($this->usuario('administrativo'));
        Livewire::test(Configuracion::class)->assertForbidden();

        $this->actingAs($this->usuario('admin'));
        Livewire::test(Configuracion::class)
            ->set('comision_porcentaje', '5.00')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertSame('5.00', \App\Models\Configuracion::actual()->fresh()->comision_porcentaje);
    }
}
