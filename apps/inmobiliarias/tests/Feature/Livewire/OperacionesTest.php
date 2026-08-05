<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Operaciones\Index;
use App\Livewire\Operaciones\Show;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Operacion;
use App\Models\Propiedad;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OperacionesTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_agente_crea_una_operacion_y_queda_asignada_a_si_mismo(): void
    {
        $agente = $this->usuario('agente');
        $this->actingAs($agente);

        $propiedad = Propiedad::factory()->create(['estado' => 'disponible']);

        Livewire::test(Index::class)
            ->set('propiedad_id', $propiedad->id)
            ->set('tipo', 'venta')
            ->set('fecha_inicio', '2026-09-01')
            ->set('monto', '100000')
            ->call('guardar')
            ->assertHasNoErrors();

        $operacion = Operacion::firstOrFail();
        $this->assertSame($agente->id, $operacion->agente_id);
    }

    public function test_un_agente_no_puede_ver_la_operacion_de_otro_agente(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');
        $ajena = Operacion::factory()->create(['agente_id' => $otroAgente->id]);

        $this->actingAs($agente);

        Livewire::test(Show::class, ['operacion' => $ajena])->assertForbidden();
    }

    public function test_el_dueno_de_la_operacion_asigna_una_parte_y_genera_el_plan_de_cuotas(): void
    {
        $agente = $this->usuario('agente');
        $operacion = Operacion::factory()->create(['agente_id' => $agente->id]);
        $comprador = Cliente::factory()->create();

        $this->actingAs($agente);

        Livewire::test(Show::class, ['operacion' => $operacion])
            ->set('parte_cliente_id', $comprador->id)
            ->set('parte_rol', 'comprador')
            ->call('asignarParte')
            ->assertHasNoErrors()
            ->set('cantidad_cuotas', 2)
            ->set('fecha_primer_vencimiento', '2026-10-01')
            ->set('monto_por_cuota', '50000')
            ->call('generarPlan')
            ->assertHasNoErrors();

        $this->assertCount(1, $operacion->fresh()->partes);
        $this->assertCount(2, $operacion->fresh()->cuotas);
    }

    public function test_el_dueno_cierra_la_operacion_y_se_genera_la_comision(): void
    {
        Configuracion::actual()->update(['comision_porcentaje' => '4.00']);

        $agente = $this->usuario('agente');
        $propiedad = Propiedad::factory()->create(['estado' => 'reservado']);
        $operacion = Operacion::factory()->deTipo('venta')->dePropiedad($propiedad)->create([
            'agente_id' => $agente->id,
            'monto' => '100000.00',
        ]);

        $this->actingAs($agente);

        Livewire::test(Show::class, ['operacion' => $operacion])->call('cerrar');

        $this->assertSame('cerrada', $operacion->fresh()->estado);
        $this->assertSame('vendido', $propiedad->fresh()->estado);
        $this->assertNotNull($operacion->fresh()->comision);
    }
}
