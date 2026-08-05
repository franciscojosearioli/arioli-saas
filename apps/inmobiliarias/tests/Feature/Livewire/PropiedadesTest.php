<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Catalogo\Propiedades;
use App\Models\Desarrollo;
use App\Models\Propiedad;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PropiedadesTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_agente_crea_una_propiedad(): void
    {
        $this->actingAs($this->usuario('agente'));

        Livewire::test(Propiedades::class)
            ->set('tipo', 'casa')
            ->set('titulo', 'Casa 3 dormitorios en Nueva Córdoba')
            ->set('servicios', 'agua corriente, gas natural')
            ->call('guardar')
            ->assertHasNoErrors();

        $propiedad = Propiedad::where('titulo', 'Casa 3 dormitorios en Nueva Córdoba')->firstOrFail();
        $this->assertSame(['agua corriente', 'gas natural'], $propiedad->servicios);
        $this->assertSame('disponible', $propiedad->estado);
    }

    public function test_solo_lectura_no_puede_crear_una_propiedad(): void
    {
        $this->actingAs($this->usuario('solo-lectura'));

        Livewire::test(Propiedades::class)
            ->set('tipo', 'casa')
            ->set('titulo', 'x')
            ->call('guardar')
            ->assertForbidden();
    }

    public function test_rechaza_un_lote_duplicado_en_la_misma_manzana_y_desarrollo(): void
    {
        $this->actingAs($this->usuario('admin'));

        $desarrollo = Desarrollo::factory()->create();
        Propiedad::factory()->deDesarrollo($desarrollo, manzana: '5', numeroLote: '12')->create();

        Livewire::test(Propiedades::class)
            ->set('desarrollo_id', $desarrollo->id)
            ->set('tipo', 'loteo')
            ->set('titulo', 'Lote 12')
            ->set('manzana', '5')
            ->set('numero_lote', '12')
            ->call('guardar')
            ->assertHasErrors(['manzana']);
    }

    public function test_rechaza_un_wkt_de_ubicacion_invalido(): void
    {
        $this->actingAs($this->usuario('admin'));

        // Solo el formato — un WKT válido dispara ST_GeomFromText (MySQL)
        // en guardar(), que no corre contra el sqlite de los tests (ver
        // Propiedad::guardarUbicacion()).
        Livewire::test(Propiedades::class)
            ->set('tipo', 'casa')
            ->set('titulo', 'Casa con ubicación inválida')
            ->set('ubicacion_wkt', 'CIRCLE(1 2, 5)')
            ->call('guardar')
            ->assertHasErrors('ubicacion_wkt');
    }

    public function test_guardar_ubicacion_rechaza_wkt_invalido_a_nivel_de_modelo(): void
    {
        // Defensa en profundidad: guardarUbicacion() nunca debe confiar
        // en que el llamador ya validó — reintenta la validación antes
        // de interpolar el WKT en la raw query (ver el comentario en el
        // modelo). Nada de esto toca MySQL: falla antes de llegar ahí.
        $propiedad = Propiedad::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $propiedad->guardarUbicacion('POINT(0 0); DROP TABLE propiedades;--');
    }

    public function test_guardar_ubicacion_con_valor_vacio_no_falla(): void
    {
        $propiedad = Propiedad::factory()->create();

        $propiedad->guardarUbicacion(null);
        $propiedad->guardarUbicacion('');

        $this->assertNull($propiedad->ubicacionComoWkt());
    }

    public function test_el_filtro_por_estado_solo_muestra_las_propiedades_de_ese_estado(): void
    {
        $this->actingAs($this->usuario('admin'));

        Propiedad::factory()->create(['titulo' => 'Disponible A', 'estado' => 'disponible']);
        Propiedad::factory()->create(['titulo' => 'Vendida B', 'estado' => 'vendido']);

        Livewire::test(Propiedades::class)
            ->set('filtroEstado', 'vendido')
            ->assertSee('Vendida B')
            ->assertDontSee('Disponible A');
    }
}
