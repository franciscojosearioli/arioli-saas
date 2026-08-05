<?php

namespace Tests\Feature\Api;

use App\Models\Cliente;
use App\Models\Comision;
use App\Models\Configuracion;
use App\Models\Cuota;
use App\Models\Operacion;
use App\Models\Propiedad;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OperacionesFinanzasApiTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_agente_abre_una_operacion_le_asigna_partes_y_genera_el_plan_de_cuotas(): void
    {
        $agente = $this->usuario('agente');
        Sanctum::actingAs($agente);

        $propiedad = Propiedad::factory()->create(['estado' => 'disponible']);
        $comprador = Cliente::factory()->create();

        $operacion = $this->postJson('/api/v1/operaciones', [
            'propiedad_id' => $propiedad->id,
            'tipo' => 'venta',
            'fecha_inicio' => '2026-09-01',
            'monto' => '100000.00',
        ])->assertCreated()->json('data');

        $this->postJson("/api/v1/operaciones/{$operacion['id']}/partes", [
            'cliente_id' => $comprador->id,
            'rol' => 'comprador',
        ])->assertOk()->assertJsonPath('data.partes.0.rol', 'comprador');

        $this->postJson("/api/v1/operaciones/{$operacion['id']}/plan-de-cuotas", [
            'cantidad_cuotas' => 3,
            'fecha_primer_vencimiento' => '2026-10-01',
            'monto_por_cuota' => '33333.33',
        ])->assertOk()->assertJsonPath('data.cuotas_count', 3);
    }

    public function test_un_agente_no_puede_ver_ni_editar_la_operacion_de_otro_agente(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        $ajena = Operacion::factory()->create(['agente_id' => $otroAgente->id]);

        Sanctum::actingAs($agente);

        $this->getJson("/api/v1/operaciones/{$ajena->id}")->assertForbidden();
        $this->postJson("/api/v1/operaciones/{$ajena->id}/cerrar")->assertForbidden();
    }

    public function test_cerrar_una_venta_actualiza_la_propiedad_y_genera_la_comision_del_agente(): void
    {
        Configuracion::actual()->update(['comision_porcentaje' => '4.00']);

        $agente = $this->usuario('agente');
        $propiedad = Propiedad::factory()->create(['estado' => 'reservado']);
        $operacion = Operacion::factory()->deTipo('venta')->dePropiedad($propiedad)->create([
            'agente_id' => $agente->id,
            'monto' => '100000.00',
        ]);

        Sanctum::actingAs($agente);

        $this->postJson("/api/v1/operaciones/{$operacion->id}/cerrar")
            ->assertOk()
            ->assertJsonPath('data.estado', 'cerrada')
            ->assertJsonPath('data.comision.monto', '4000.00');

        $this->assertSame('vendido', $propiedad->fresh()->estado);
    }

    public function test_un_agente_no_puede_registrar_pagos_eso_es_trabajo_administrativo(): void
    {
        $agente = $this->usuario('agente');
        $operacion = Operacion::factory()->create(['agente_id' => $agente->id]);
        $cuota = Cuota::factory()->deOperacion($operacion)->create(['monto' => '10000.00']);

        Sanctum::actingAs($agente);

        $this->postJson('/api/v1/pagos', [
            'cuota_id' => $cuota->id,
            'monto' => '10000.00',
            'fecha' => '2026-09-01',
            'medio_pago' => 'efectivo',
        ])->assertForbidden();
    }

    public function test_administrativo_registra_un_pago_y_la_cuota_pasa_a_pagada(): void
    {
        $administrativo = $this->usuario('administrativo');
        $operacion = Operacion::factory()->create();
        $cuota = Cuota::factory()->deOperacion($operacion)->create(['monto' => '10000.00']);

        Sanctum::actingAs($administrativo);

        $this->postJson('/api/v1/pagos', [
            'cuota_id' => $cuota->id,
            'monto' => '10000.00',
            'fecha' => '2026-09-01',
            'medio_pago' => 'efectivo',
        ])->assertCreated();

        $this->assertSame('pagada', $cuota->fresh()->estado);
    }

    public function test_administrativo_cierra_la_caja_del_dia_y_agente_no_puede(): void
    {
        $administrativo = $this->usuario('administrativo');
        $agente = $this->usuario('agente');

        $operacion = Operacion::factory()->create();
        $cuota = Cuota::factory()->deOperacion($operacion)->create(['monto' => '5000.00']);
        $cuota->registrarPago(['monto' => '5000.00', 'fecha' => '2026-09-05', 'medio_pago' => 'efectivo']);

        Sanctum::actingAs($agente);
        $this->postJson('/api/v1/arqueos-caja', [
            'fecha' => '2026-09-05',
            'monto_contado' => '5000.00',
        ])->assertForbidden();

        Sanctum::actingAs($administrativo);
        $this->postJson('/api/v1/arqueos-caja', [
            'fecha' => '2026-09-05',
            'monto_contado' => '5000.00',
        ])->assertCreated()->assertJsonPath('data.monto_esperado', '5000.00');
    }

    public function test_un_agente_ve_su_propia_comision_pero_no_la_de_otro(): void
    {
        $agente = $this->usuario('agente');
        $otroAgente = $this->usuario('agente');

        $propia = Comision::factory()->create(['agente_id' => $agente->id]);
        $ajena = Comision::factory()->create(['agente_id' => $otroAgente->id]);

        Sanctum::actingAs($agente);

        $this->getJson("/api/v1/comisiones/{$propia->id}")->assertOk();
        $this->getJson("/api/v1/comisiones/{$ajena->id}")->assertForbidden();
    }

    public function test_administrativo_liquida_una_comision_y_agente_no_puede(): void
    {
        $agente = $this->usuario('agente');
        $administrativo = $this->usuario('administrativo');
        $comision = Comision::factory()->create(['agente_id' => $agente->id, 'estado' => 'pendiente']);

        Sanctum::actingAs($agente);
        $this->postJson("/api/v1/comisiones/{$comision->id}/liquidar")->assertForbidden();

        Sanctum::actingAs($administrativo);
        $this->postJson("/api/v1/comisiones/{$comision->id}/liquidar")
            ->assertOk()
            ->assertJsonPath('data.estado', 'liquidada');
    }

    public function test_se_puede_adjuntar_un_documento_a_una_propiedad_via_el_tipo_documentable(): void
    {
        Sanctum::actingAs($this->usuario('agente'));

        $propiedad = Propiedad::factory()->create();

        $this->postJson('/api/v1/documentos', [
            'documentable_type' => 'propiedad',
            'documentable_id' => $propiedad->id,
            'tipo' => 'escritura',
            'nombre' => 'Escritura Lote 5',
            'archivo' => 'documentos/escritura-lote-5.pdf',
        ])->assertCreated()->assertJsonPath('data.documentable_type', 'Propiedad');

        $this->assertCount(1, $propiedad->fresh()->documentos);
    }

    public function test_solo_admin_puede_ver_y_actualizar_la_configuracion_del_tenant(): void
    {
        Sanctum::actingAs($this->usuario('administrativo'));
        $this->getJson('/api/v1/configuracion')->assertForbidden();

        Sanctum::actingAs($this->usuario('admin'));
        $this->getJson('/api/v1/configuracion')->assertOk();
        $this->patchJson('/api/v1/configuracion', ['comision_porcentaje' => '5.00'])
            ->assertOk()
            ->assertJsonPath('data.comision_porcentaje', '5.00');
    }
}
