<?php

namespace Tests\Feature;

use App\Models\Configuracion;
use App\Models\FotoPropiedad;
use App\Models\Operacion;
use App\Models\OutboxEvent;
use App\Models\Propiedad;
use App\Models\Publicacion;
use App\Models\PublicacionCanal;
use App\Services\Publicaciones\ContenidoPublicacion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicacionesTest extends TestCase
{
    public function test_activar_un_canal_lo_crea_una_sola_vez(): void
    {
        $publicacion = Publicacion::factory()->create();

        $canal = $publicacion->activarCanal('marketplace');
        $mismoCanal = $publicacion->activarCanal('marketplace');

        $this->assertSame($canal->id, $mismoCanal->id);
        $this->assertCount(1, $publicacion->fresh()->canales);
    }

    public function test_actualizar_un_campo_publicable_de_una_propiedad_publicada_encola_un_evento(): void
    {
        $propiedad = Propiedad::factory()->create(['precio' => '100000']);
        Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);

        $propiedad->update(['precio' => '120000']);

        $this->assertSame(1, OutboxEvent::pendientes()->count());
        $this->assertSame('PropiedadActualizada', OutboxEvent::first()->evento);
    }

    public function test_actualizar_una_propiedad_sin_publicacion_no_encola_nada(): void
    {
        $propiedad = Propiedad::factory()->create(['precio' => '100000']);

        $propiedad->update(['precio' => '120000']);

        $this->assertSame(0, OutboxEvent::count());
    }

    public function test_agregar_una_foto_a_una_propiedad_publicada_encola_un_evento(): void
    {
        $propiedad = Propiedad::factory()->create();
        Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);

        FotoPropiedad::factory()->create(['propiedad_id' => $propiedad->id]);

        $this->assertSame(1, OutboxEvent::pendientes()->count());
    }

    public function test_el_contenido_de_publicacion_incluye_el_tipo_de_operacion_activa_y_la_galeria_ordenada(): void
    {
        $propiedad = Propiedad::factory()->create();
        Operacion::factory()->deTipo('venta')->dePropiedad($propiedad)->create(['estado' => 'abierta']);
        FotoPropiedad::factory()->create(['propiedad_id' => $propiedad->id, 'url' => 'b.jpg', 'orden' => 2]);
        FotoPropiedad::factory()->create(['propiedad_id' => $propiedad->id, 'url' => 'a.jpg', 'orden' => 1]);

        $contenido = ContenidoPublicacion::fromPropiedad($propiedad->fresh());

        $this->assertSame('venta', $contenido->tipoOperacion);
        $this->assertSame(['a.jpg', 'b.jpg'], $contenido->galeria);
    }

    public function test_el_worker_publica_en_el_marketplace_y_guarda_el_external_id(): void
    {
        config(['marketplace.api_url' => 'http://marketplace.test']);
        Http::fake(['marketplace.test/api/publications' => Http::response(['id' => 'mkt-123'], 201)]);

        $propiedad = Propiedad::factory()->create();
        $publicacion = Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);
        $publicacion->activarCanal('marketplace');
        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadActualizada',
        ]);

        Artisan::call('publicaciones:sincronizar');

        $canal = PublicacionCanal::first();
        $this->assertSame('publicada', $canal->estado);
        $this->assertSame('mkt-123', $canal->external_id);
        $this->assertNotNull(OutboxEvent::first()->procesado_en);
    }

    public function test_si_el_canal_falla_registra_el_error_sin_tumbar_la_sincronizacion(): void
    {
        config(['marketplace.api_url' => 'http://marketplace.test']);
        Http::fake(['marketplace.test/*' => Http::response(['message' => 'boom'], 500)]);

        $propiedad = Propiedad::factory()->create();
        $publicacion = Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);
        $publicacion->activarCanal('marketplace');
        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadActualizada',
        ]);

        Artisan::call('publicaciones:sincronizar');

        $canal = PublicacionCanal::first();
        $this->assertNotNull($canal->ultimo_error);
        $this->assertSame(1, $canal->intentos);
        // Un solo intento no agota MAX_INTENTOS -> sigue en 'publicando',
        // listo para que el próximo evento lo reintente.
        $this->assertNotSame('error', $canal->estado);
        $this->assertNotNull(OutboxEvent::first()->procesado_en);
    }

    public function test_el_sitio_web_del_tenant_usa_su_propia_url_configurada(): void
    {
        Configuracion::actual()->update([
            'sitio_web_url' => 'http://sitio-tenant.test',
            'sitio_web_api_key' => 'clave-secreta',
        ]);
        Http::fake(['sitio-tenant.test/*' => Http::response(['id' => 'sitio-987'], 201)]);

        $propiedad = Propiedad::factory()->create();
        $publicacion = Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);
        $publicacion->activarCanal('sitio_web');
        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadActualizada',
        ]);

        Artisan::call('publicaciones:sincronizar');

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer clave-secreta'));
        $this->assertSame('publicada', PublicacionCanal::first()->estado);
    }

    public function test_un_canal_pausado_no_se_sincroniza(): void
    {
        config(['marketplace.api_url' => 'http://marketplace.test']);
        Http::fake();

        $propiedad = Propiedad::factory()->create();
        $publicacion = Publicacion::factory()->create(['propiedad_id' => $propiedad->id]);
        $canal = $publicacion->activarCanal('marketplace');
        $canal->pausar();

        OutboxEvent::create([
            'aggregate_type' => Propiedad::class,
            'aggregate_id' => $propiedad->id,
            'evento' => 'PropiedadActualizada',
        ]);

        Artisan::call('publicaciones:sincronizar');

        Http::assertNothingSent();
        $this->assertSame('pausada', $canal->fresh()->estado);
    }
}
