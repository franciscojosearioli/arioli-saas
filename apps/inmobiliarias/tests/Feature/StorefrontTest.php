<?php

namespace Tests\Feature;

use App\Models\Constructora;
use App\Models\Desarrollo;
use App\Models\Propiedad;
use App\Models\Publicacion;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    public function test_la_home_solo_muestra_propiedades_publicadas(): void
    {
        $publicada = Propiedad::factory()->create(['titulo' => 'Casa publicada']);
        Publicacion::factory()->create(['propiedad_id' => $publicada->id]);

        Propiedad::factory()->create(['titulo' => 'Casa sin publicar']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Casa publicada')
            ->assertDontSee('Casa sin publicar');
    }

    public function test_filtrar_por_provincia(): void
    {
        $enCordoba = Propiedad::factory()->create(['titulo' => 'Casa en Córdoba', 'provincia' => 'Córdoba']);
        Publicacion::factory()->create(['propiedad_id' => $enCordoba->id]);

        $enBsAs = Propiedad::factory()->create(['titulo' => 'Casa en Buenos Aires', 'provincia' => 'Buenos Aires']);
        Publicacion::factory()->create(['propiedad_id' => $enBsAs->id]);

        $this->get('/?provincia=Córdoba')
            ->assertOk()
            ->assertSee('Casa en Córdoba')
            ->assertDontSee('Casa en Buenos Aires');
    }

    public function test_ver_la_ficha_de_una_propiedad_publicada(): void
    {
        $propiedad = Propiedad::factory()->create(['titulo' => 'Departamento en el centro', 'descripcion' => 'Luminoso y a estrenar.']);
        Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);

        $this->get(route('storefront.propiedad', $propiedad))
            ->assertOk()
            ->assertSee('Departamento en el centro')
            ->assertSee('Luminoso y a estrenar.');
    }

    public function test_una_propiedad_sin_publicar_no_es_visible_publicamente(): void
    {
        $propiedad = Propiedad::factory()->create();

        $this->get(route('storefront.propiedad', $propiedad))->assertNotFound();
    }

    public function test_ver_el_mapa_de_un_desarrollo(): void
    {
        $desarrollo = Desarrollo::factory()->create(['nombre' => 'Loteo Las Acacias']);

        $this->get(route('storefront.desarrollo', $desarrollo))
            ->assertOk()
            ->assertSee('Loteo Las Acacias');
    }

    public function test_el_conteo_de_unidades_del_desarrollo_solo_cuenta_publicadas(): void
    {
        $desarrollo = Desarrollo::factory()->create(['tipo' => 'loteo']);

        $publicada = Propiedad::factory()->create(['desarrollo_id' => $desarrollo->id]);
        Publicacion::factory()->create(['propiedad_id' => $publicada->id]);

        Propiedad::factory()->create(['desarrollo_id' => $desarrollo->id]);

        $this->get(route('storefront.desarrollo', $desarrollo))
            ->assertOk()
            ->assertSee('1 lote publicado');
    }

    public function test_ver_el_perfil_de_una_constructora_con_sus_desarrollos(): void
    {
        $constructora = Constructora::factory()->create(['nombre' => 'Edisur']);
        Desarrollo::factory()->create(['constructora_id' => $constructora->id, 'nombre' => 'Barrio Los Robles']);

        $this->get(route('storefront.constructora', $constructora))
            ->assertOk()
            ->assertSee('Edisur')
            ->assertSee('Barrio Los Robles');
    }
}
