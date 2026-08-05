<?php

namespace Tests\Feature;

use App\Models\ArqueoCaja;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Operacion;
use App\Models\Propiedad;
use App\Models\User;
use Tests\TestCase;

class OperacionesFinanzasTest extends TestCase
{
    public function test_una_operacion_asigna_partes_con_rol(): void
    {
        $operacion = Operacion::factory()->deTipo('venta')->create();
        $comprador = Cliente::factory()->create();
        $vendedor = Cliente::factory()->create();

        $operacion->asignarParte($comprador, 'comprador');
        $operacion->asignarParte($vendedor, 'vendedor');

        $this->assertSame('comprador', $operacion->partes->firstWhere('id', $comprador->id)->pivot->rol);
        $this->assertSame('vendedor', $operacion->partes->firstWhere('id', $vendedor->id)->pivot->rol);
        $this->assertCount(2, $operacion->fresh()->partes);
    }

    public function test_generar_plan_de_cuotas_crea_las_cuotas_con_vencimiento_mensual(): void
    {
        $operacion = Operacion::factory()->deTipo('venta')->create();

        $operacion->generarPlanDeCuotas(3, '2026-09-01', '10000.00');

        $cuotas = $operacion->fresh()->cuotas()->orderBy('numero')->get();
        $this->assertCount(3, $cuotas);
        $this->assertSame('2026-09-01', $cuotas[0]->fecha_vencimiento->toDateString());
        $this->assertSame('2026-10-01', $cuotas[1]->fecha_vencimiento->toDateString());
        $this->assertSame('2026-11-01', $cuotas[2]->fecha_vencimiento->toDateString());
    }

    public function test_un_pago_parcial_deja_la_cuota_en_estado_parcial(): void
    {
        $operacion = Operacion::factory()->create();
        $operacion->generarPlanDeCuotas(1, '2026-09-01', '10000.00');
        $cuota = $operacion->cuotas()->first();

        $cuota->registrarPago(['monto' => '4000.00', 'fecha' => '2026-09-01', 'medio_pago' => 'efectivo']);

        $this->assertSame('parcial', $cuota->fresh()->estado);
    }

    public function test_completar_el_monto_de_la_cuota_con_varios_pagos_la_marca_pagada(): void
    {
        $operacion = Operacion::factory()->create();
        $operacion->generarPlanDeCuotas(1, '2026-09-01', '10000.00');
        $cuota = $operacion->cuotas()->first();

        $cuota->registrarPago(['monto' => '4000.00', 'fecha' => '2026-09-01', 'medio_pago' => 'efectivo']);
        $cuota->registrarPago(['monto' => '6000.00', 'fecha' => '2026-09-15', 'medio_pago' => 'transferencia']);

        $this->assertSame('pagada', $cuota->fresh()->estado);
        $this->assertCount(2, $cuota->fresh()->pagos);
    }

    public function test_cerrar_una_venta_marca_la_propiedad_vendida_y_genera_la_comision_del_agente(): void
    {
        Configuracion::actual()->update(['comision_porcentaje' => '4.00']);

        $propiedad = Propiedad::factory()->create(['estado' => 'reservado']);
        $agente = User::factory()->create();
        $operacion = Operacion::factory()->deTipo('venta')->dePropiedad($propiedad)->create([
            'agente_id' => $agente->id,
            'monto' => '100000.00',
        ]);

        $operacion->cerrar();

        $this->assertSame('vendido', $propiedad->fresh()->estado);
        $this->assertSame('cerrada', $operacion->fresh()->estado);
        $this->assertNotNull($operacion->fresh()->fecha_cierre);

        $comision = $operacion->fresh()->comision;
        $this->assertNotNull($comision);
        $this->assertSame($agente->id, $comision->agente_id);
        $this->assertSame('4000.00', $comision->monto);
    }

    public function test_cerrar_un_alquiler_marca_la_propiedad_alquilada(): void
    {
        $propiedad = Propiedad::factory()->create(['estado' => 'reservado']);
        $operacion = Operacion::factory()->deTipo('alquiler')->dePropiedad($propiedad)->create();

        $operacion->cerrar();

        $this->assertSame('alquilado', $propiedad->fresh()->estado);
    }

    public function test_sin_porcentaje_de_comision_configurado_no_se_genera_comision(): void
    {
        Configuracion::actual()->update(['comision_porcentaje' => null]);

        $agente = User::factory()->create();
        $operacion = Operacion::factory()->deTipo('venta')->create([
            'agente_id' => $agente->id,
            'monto' => '100000.00',
        ]);

        $operacion->cerrar();

        $this->assertNull($operacion->fresh()->comision);
    }

    public function test_cancelar_una_operacion_no_toca_el_estado_de_la_propiedad(): void
    {
        $propiedad = Propiedad::factory()->create(['estado' => 'disponible']);
        $operacion = Operacion::factory()->dePropiedad($propiedad)->create();

        $operacion->cancelar();

        $this->assertSame('cancelada', $operacion->fresh()->estado);
        $this->assertSame('disponible', $propiedad->fresh()->estado);
    }

    public function test_renovar_un_contrato_lo_encadena_y_marca_el_anterior_como_renovado(): void
    {
        $operacion = Operacion::factory()->deTipo('alquiler')->create();
        $contrato = Contrato::factory()->deOperacion($operacion)->create([
            'estado' => 'firmado',
            'fecha_inicio' => '2025-01-01',
            'fecha_fin' => '2026-01-01',
        ]);

        $renovacion = $contrato->renovar([
            'estado' => 'firmado',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2027-01-01',
        ]);

        $this->assertSame('renovado', $contrato->fresh()->estado);
        $this->assertSame($contrato->id, $renovacion->renueva_contrato_id);
        $this->assertSame($operacion->id, $renovacion->operacion_id);
        $this->assertTrue($contrato->fresh()->renovaciones->contains($renovacion));
    }

    public function test_un_documento_se_adjunta_indistintamente_a_distintas_entidades(): void
    {
        $propiedad = Propiedad::factory()->create();
        $cliente = Cliente::factory()->create();

        $docPropiedad = $propiedad->documentos()->save(Documento::factory()->make());
        $docCliente = $cliente->documentos()->save(Documento::factory()->make());

        $this->assertTrue($docPropiedad->documentable->is($propiedad));
        $this->assertTrue($docCliente->documentable->is($cliente));
    }

    public function test_el_arqueo_de_caja_solo_cuenta_los_pagos_en_efectivo_del_dia(): void
    {
        $operacion = Operacion::factory()->create();
        $operacion->generarPlanDeCuotas(1, '2026-09-01', '50000.00');
        $cuota = $operacion->cuotas()->first();

        $cuota->registrarPago(['monto' => '20000.00', 'fecha' => '2026-09-05', 'medio_pago' => 'efectivo']);
        $cuota->registrarPago(['monto' => '15000.00', 'fecha' => '2026-09-05', 'medio_pago' => 'transferencia']);
        $cuota->registrarPago(['monto' => '30000.00', 'fecha' => '2026-09-06', 'medio_pago' => 'efectivo']);

        $esperado = ArqueoCaja::calcularEsperado('2026-09-05');

        $this->assertSame('20000.00', $esperado);
    }

    public function test_un_arqueo_calcula_su_diferencia_entre_lo_contado_y_lo_esperado(): void
    {
        $arqueo = ArqueoCaja::factory()->create([
            'monto_esperado' => '50000.00',
            'monto_contado' => '49500.00',
        ]);

        $this->assertSame('-500.00', $arqueo->diferencia());
    }
}
