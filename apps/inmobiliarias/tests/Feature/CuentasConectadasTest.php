<?php

namespace Tests\Feature;

use App\Livewire\Configuracion;
use App\Models\CuentaConectada;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CuentasConectadasTest extends TestCase
{
    private function usuario(string $rol): User
    {
        Role::findOrCreate($rol, 'web');

        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_un_administrativo_no_puede_iniciar_la_conexion_con_facebook(): void
    {
        $this->actingAs($this->usuario('administrativo'));

        $this->get(route('configuracion.facebook.conectar'))->assertForbidden();
    }

    public function test_un_administrativo_no_puede_desconectar_una_cuenta(): void
    {
        $cuenta = CuentaConectada::factory()->create();

        $this->actingAs($this->usuario('administrativo'));

        $this->delete(route('configuracion.cuentas-conectadas.desconectar', $cuenta))->assertForbidden();
        $this->assertModelExists($cuenta);
    }

    public function test_admin_desconecta_una_cuenta(): void
    {
        $cuenta = CuentaConectada::factory()->create();

        $this->actingAs($this->usuario('admin'));

        $this->delete(route('configuracion.cuentas-conectadas.desconectar', $cuenta))
            ->assertRedirect(route('configuracion'));

        $this->assertModelMissing($cuenta);
    }

    public function test_la_pagina_de_configuracion_ofrece_conectar_facebook_cuando_no_hay_cuenta(): void
    {
        $this->actingAs($this->usuario('admin'));

        Livewire::test(Configuracion::class)
            ->assertSee('Conectar Facebook')
            ->assertDontSee('Desconectar');
    }

    public function test_la_pagina_de_configuracion_muestra_la_cuenta_conectada(): void
    {
        $cuenta = CuentaConectada::factory()->create(['external_account_name' => 'Inmobiliaria Demo']);

        $this->actingAs($this->usuario('admin'));

        Livewire::test(Configuracion::class)
            ->assertSee('Inmobiliaria Demo')
            ->assertSee('Desconectar')
            ->assertDontSee('Conectar Facebook');
    }

    public function test_la_pagina_de_configuracion_marca_una_cuenta_que_requiere_reconexion(): void
    {
        CuentaConectada::factory()->requiereReconexion()->create();

        $this->actingAs($this->usuario('admin'));

        Livewire::test(Configuracion::class)->assertSee('Requiere reconexión');
    }

    public function test_requiere_reconexion_refleja_el_estado(): void
    {
        $cuenta = CuentaConectada::factory()->create();
        $this->assertFalse($cuenta->requiereReconexion());

        $cuenta->marcarRequiereReconexion('El token expiró.');

        $this->assertTrue($cuenta->fresh()->requiereReconexion());
        $this->assertSame('El token expiró.', $cuenta->fresh()->ultimo_error);
    }

    public function test_esta_por_vencer_solo_dentro_del_margen_y_antes_de_vencer(): void
    {
        // make() sin persistir: las tres cuentan como "canal" facebook por
        // default, y cuentas_conectadas tiene un unique('canal') que solo
        // deja una fila por canal en toda la tabla.
        $vigente = CuentaConectada::factory()->make(['token_expira_en' => now()->addDays(30)]);
        $porVencer = CuentaConectada::factory()->make(['token_expira_en' => now()->addDays(3)]);
        $yaVencido = CuentaConectada::factory()->make(['token_expira_en' => now()->subDay()]);

        $this->assertFalse($vigente->estaPorVencer());
        $this->assertTrue($porVencer->estaPorVencer());
        $this->assertFalse($yaVencido->estaPorVencer());
    }

    public function test_el_job_diario_marca_requiere_reconexion_si_meta_rechaza_el_token(): void
    {
        Http::fake(['graph.facebook.com/*/me*' => Http::response(['error' => ['message' => 'boom']], 400)]);

        $cuenta = CuentaConectada::factory()->create(['token_expira_en' => now()->addDays(60)]);

        Artisan::call('cuentas-conectadas:revisar-vencimientos');

        $this->assertTrue($cuenta->fresh()->requiereReconexion());
    }

    public function test_el_job_diario_marca_requiere_reconexion_si_el_token_esta_por_vencer(): void
    {
        Http::fake(['graph.facebook.com/*/me*' => Http::response(['id' => '123'], 200)]);

        $cuenta = CuentaConectada::factory()->create(['token_expira_en' => now()->addDays(3)]);

        Artisan::call('cuentas-conectadas:revisar-vencimientos');

        $this->assertTrue($cuenta->fresh()->requiereReconexion());
    }

    public function test_el_job_diario_no_toca_una_cuenta_vigente(): void
    {
        Http::fake(['graph.facebook.com/*/me*' => Http::response(['id' => '123'], 200)]);

        $cuenta = CuentaConectada::factory()->create(['token_expira_en' => now()->addDays(60)]);

        Artisan::call('cuentas-conectadas:revisar-vencimientos');

        $this->assertSame('activa', $cuenta->fresh()->estado);
    }

    public function test_el_job_diario_no_revisa_cuentas_que_ya_requieren_reconexion(): void
    {
        Http::fake();

        CuentaConectada::factory()->requiereReconexion()->create();

        Artisan::call('cuentas-conectadas:revisar-vencimientos');

        Http::assertNothingSent();
    }
}
