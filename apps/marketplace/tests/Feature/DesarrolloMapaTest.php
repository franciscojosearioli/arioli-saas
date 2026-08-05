<?php

namespace Tests\Feature;

use App\Models\Desarrollo;
use App\Models\FichaPropiedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesarrolloMapaTest extends TestCase
{
    use RefreshDatabase;

    public function test_ver_el_mapa_de_un_desarrollo(): void
    {
        $desarrollo = Desarrollo::factory()->create(['nombre' => 'Loteo Las Acacias']);

        $this->get(route('desarrollos.show', $desarrollo))
            ->assertOk()
            ->assertSee('Loteo Las Acacias');
    }

    public function test_el_mapa_solo_incluye_las_fichas_del_desarrollo(): void
    {
        $desarrollo = Desarrollo::factory()->create();
        $otro = Desarrollo::factory()->create();

        FichaPropiedad::factory()->create(['titulo' => 'Lote propio', 'desarrollo_id' => $desarrollo->id]);
        FichaPropiedad::factory()->create(['titulo' => 'Lote de otro desarrollo', 'desarrollo_id' => $otro->id]);
        FichaPropiedad::factory()->create(['titulo' => 'Lote sin desarrollo', 'desarrollo_id' => null]);

        // $unidades (con geometría) siempre da vacío en sqlite — ver el
        // guard de driver en ubicacionComoGeoJson(). Lo único
        // verificable acá sin MySQL real es que el conteo total
        // (totalUnidades, un WHERE plano) está bien scopeado por
        // desarrollo_id — el redibujado del mapa en sí se verificó
        // manualmente contra MySQL real.
        $this->get(route('desarrollos.show', $desarrollo))
            ->assertOk()
            ->assertSee('1 lote publicado');
    }

    public function test_un_desarrollo_sin_unidades_publicadas_no_falla(): void
    {
        $desarrollo = Desarrollo::factory()->create();

        $this->get(route('desarrollos.show', $desarrollo))
            ->assertOk()
            ->assertSee('todavía no tiene unidades');
    }
}
