<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\CuentaConectada;
use App\Services\Publicaciones\MetaGraphClient;
use DateTimeInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

/**
 * §09/§04, Fase 4: "conecta su página de Facebook, su cuenta profesional
 * de Instagram... solo Owner/Admin, nunca un agente" — reusa la Policy de
 * Configuracion en vez de una nueva, es la misma acción sensible ya
 * modelada ahí (Gate::before ya deja pasar a admin, todo lo demás cae en
 * ConfiguracionPolicy::update() -> false).
 */
class CuentasConectadasController extends Controller
{
    public function conectarFacebook(): RedirectResponse
    {
        $this->authorize('update', Configuracion::actual());

        return Socialite::driver('facebook')
            ->redirectUrl(route('configuracion.facebook.callback'))
            ->scopes([
                'pages_show_list', 'pages_read_engagement', 'pages_manage_posts',
                'instagram_basic', 'instagram_content_publish',
            ])
            ->redirect();
    }

    public function callbackFacebook(MetaGraphClient $graph): RedirectResponse|View
    {
        $this->authorize('update', Configuracion::actual());

        $usuarioMeta = Socialite::driver('facebook')
            ->redirectUrl(route('configuracion.facebook.callback'))
            ->user();

        $tokenLargo = $graph->intercambiarPorTokenDeLargaDuracion($usuarioMeta->token);
        $paginas = $graph->listarPaginas($tokenLargo['access_token']);

        if (empty($paginas)) {
            return redirect()->route('configuracion')
                ->with('error', 'No se encontró ninguna página de Facebook administrada por esta cuenta.');
        }

        if (count($paginas) === 1) {
            $this->conectarPagina($paginas[0], $graph, $tokenLargo['expira_en']);

            return redirect()->route('configuracion')->with('status', 'Cuenta de Facebook conectada.');
        }

        // Más de una página: no adivinar cuál — el tenant elige.
        $estado = (string) Str::uuid();
        Cache::put("oauth_facebook_paginas_{$estado}", [
            'paginas' => $paginas,
            'expira_en' => $tokenLargo['expira_en'],
        ], now()->addMinutes(10));

        return view('configuracion.elegir-pagina', ['paginas' => $paginas, 'estado' => $estado]);
    }

    public function elegirPagina(Request $request, MetaGraphClient $graph): RedirectResponse
    {
        $this->authorize('update', Configuracion::actual());

        $datos = $request->validate([
            'estado' => ['required', 'string'],
            'pagina_id' => ['required', 'string'],
        ]);

        $cache = Cache::pull("oauth_facebook_paginas_{$datos['estado']}");
        abort_if(! $cache, 419, 'La sesión de conexión expiró — probá de nuevo.');

        $pagina = collect($cache['paginas'])->firstWhere('id', $datos['pagina_id']);
        abort_unless($pagina, 404);

        $this->conectarPagina($pagina, $graph, $cache['expira_en']);

        return redirect()->route('configuracion')->with('status', 'Cuenta de Facebook conectada.');
    }

    public function desconectar(CuentaConectada $cuentaConectada): RedirectResponse
    {
        $this->authorize('update', Configuracion::actual());

        $cuentaConectada->delete();

        return redirect()->route('configuracion')->with('status', 'Cuenta desconectada.');
    }

    /**
     * Guarda la Página como canal 'facebook' y, si tiene una cuenta de
     * Instagram vinculada, también 'instagram' — ambas usan el mismo
     * Page Access Token (la Graph API de Instagram no tiene un token
     * separado cuando se llega a través de la Página, §09).
     */
    private function conectarPagina(array $pagina, MetaGraphClient $graph, DateTimeInterface $expiraEn): void
    {
        CuentaConectada::updateOrCreate(
            ['canal' => 'facebook'],
            [
                'external_account_id' => $pagina['id'],
                'external_account_name' => $pagina['name'],
                'access_token' => $pagina['access_token'],
                'scopes' => ['pages_show_list', 'pages_read_engagement', 'pages_manage_posts'],
                'token_expira_en' => $expiraEn,
                'estado' => 'activa',
                'ultimo_error' => null,
            ]
        );

        $instagram = $graph->cuentaInstagramVinculada($pagina['id'], $pagina['access_token']);

        if ($instagram) {
            CuentaConectada::updateOrCreate(
                ['canal' => 'instagram'],
                [
                    'external_account_id' => $instagram['id'],
                    'external_account_name' => $instagram['username'],
                    'access_token' => $pagina['access_token'],
                    'scopes' => ['instagram_basic', 'instagram_content_publish'],
                    'token_expira_en' => $expiraEn,
                    'estado' => 'activa',
                    'ultimo_error' => null,
                ]
            );
        }
    }
}
