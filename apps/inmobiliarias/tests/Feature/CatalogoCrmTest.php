<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Constructora;
use App\Models\Desarrollo;
use App\Models\Lead;
use App\Models\Propiedad;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CatalogoCrmTest extends TestCase
{
    public function test_una_constructora_agrupa_varios_desarrollos(): void
    {
        $constructora = Constructora::factory()->create(['nombre' => 'Edisur']);
        Desarrollo::factory()->count(2)->deConstructora($constructora)->create();

        $this->assertCount(2, $constructora->fresh()->desarrollos);
    }

    public function test_un_desarrollo_agrupa_lotes_de_un_tipo(): void
    {
        $desarrollo = Desarrollo::factory()->create(['tipo' => 'loteo']);
        Propiedad::factory()->count(3)->deDesarrollo($desarrollo)->create();

        $this->assertCount(3, $desarrollo->fresh()->propiedades);
        $this->assertTrue($desarrollo->propiedades->every(fn (Propiedad $p) => $p->tipo === 'loteo'));
    }

    public function test_un_lote_es_unico_dentro_de_su_manzana_y_desarrollo(): void
    {
        $desarrollo = Desarrollo::factory()->create();
        Propiedad::factory()->deDesarrollo($desarrollo, manzana: '5', numeroLote: '12')->create();

        $this->expectException(QueryException::class);

        Propiedad::factory()->deDesarrollo($desarrollo, manzana: '5', numeroLote: '12')->create();
    }

    public function test_el_mismo_numero_de_lote_es_valido_en_otra_manzana_o_desarrollo(): void
    {
        $desarrollo = Desarrollo::factory()->create();
        Propiedad::factory()->deDesarrollo($desarrollo, manzana: '5', numeroLote: '12')->create();

        // Misma manzana en OTRO desarrollo: válido.
        $otroDesarrollo = Desarrollo::factory()->create();
        Propiedad::factory()->deDesarrollo($otroDesarrollo, manzana: '5', numeroLote: '12')->create();

        // Otra manzana en el MISMO desarrollo: válido.
        Propiedad::factory()->deDesarrollo($desarrollo, manzana: '6', numeroLote: '12')->create();

        $this->assertSame(3, Propiedad::count());
    }

    public function test_un_cliente_es_propietario_de_varias_propiedades_sin_ser_un_tipo_aparte(): void
    {
        $cliente = Cliente::factory()->create(['nombre' => 'Ana Propietaria']);
        Propiedad::factory()->count(2)->conPropietario($cliente)->create();

        $this->assertCount(2, $cliente->fresh()->propiedades);
        $this->assertSame($cliente->id, Propiedad::first()->propietario->id);
    }

    public function test_convertir_un_lead_lo_vincula_a_un_cliente_y_cambia_su_estado(): void
    {
        $lead = Lead::factory()->create(['estado' => 'calificado']);
        $cliente = Cliente::factory()->create();

        $lead->convertir($cliente);

        $this->assertSame('convertido', $lead->fresh()->estado);
        $this->assertSame($cliente->id, $lead->fresh()->cliente_id);
    }

    public function test_un_lead_interesado_en_una_propiedad_puntual_la_referencia(): void
    {
        $propiedad = Propiedad::factory()->create();
        $lead = Lead::factory()->create(['propiedad_id' => $propiedad->id]);

        $this->assertTrue($lead->propiedad->is($propiedad));
        $this->assertTrue($propiedad->leads->contains($lead));
    }
}
