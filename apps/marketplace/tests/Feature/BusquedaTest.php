<?php

namespace Tests\Feature;

use App\Models\FichaPropiedad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusquedaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_busqueda_muestra_las_fichas_publicadas(): void
    {
        FichaPropiedad::factory()->create(['titulo' => 'Casa en Nueva Córdoba']);

        $this->get('/')->assertOk()->assertSee('Casa en Nueva Córdoba');
    }

    public function test_filtrar_por_tipo_de_operacion(): void
    {
        FichaPropiedad::factory()->create(['titulo' => 'Departamento en venta', 'tipo_operacion' => 'venta']);
        FichaPropiedad::factory()->create(['titulo' => 'Departamento en alquiler', 'tipo_operacion' => 'alquiler']);

        $this->get('/?tipo_operacion=alquiler')
            ->assertOk()
            ->assertSee('Departamento en alquiler')
            ->assertDontSee('Departamento en venta');
    }

    public function test_filtrar_por_rango_de_precio(): void
    {
        FichaPropiedad::factory()->create(['titulo' => 'Barata', 'precio' => '50000']);
        FichaPropiedad::factory()->create(['titulo' => 'Cara', 'precio' => '500000']);

        $this->get('/?precio_max=100000')
            ->assertOk()
            ->assertSee('Barata')
            ->assertDontSee('Cara');
    }

    public function test_ver_el_detalle_de_una_ficha_por_slug(): void
    {
        $ficha = FichaPropiedad::factory()->create([
            'titulo' => 'Loteo Los Robles',
            'slug' => 'loteo-los-robles-demo-1',
            'descripcion' => 'Un loteo con todos los servicios.',
        ]);

        $this->get("/propiedades/{$ficha->slug}")
            ->assertOk()
            ->assertSee('Loteo Los Robles')
            ->assertSee('Un loteo con todos los servicios.');
    }
}
