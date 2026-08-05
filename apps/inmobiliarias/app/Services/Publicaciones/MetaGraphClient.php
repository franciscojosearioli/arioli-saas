<?php

namespace App\Services\Publicaciones;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * §09 Fase 4: lo que Socialite no cubre del lado de Meta — el token que
 * devuelve el login OAuth estándar es de usuario y de corta vida; para
 * publicar de verdad hace falta el token de la Página (que no vence
 * mientras el usuario no revoque el acceso) y, si corresponde, la cuenta
 * de Instagram vinculada a esa Página. Tres llamadas específicas de la
 * Graph API, ninguna la resuelve un paquete genérico de OAuth.
 */
class MetaGraphClient
{
    private string $baseUrl;

    public function __construct(
        private readonly string $appId,
        private readonly string $appSecret,
        string $graphVersion = 'v19.0',
    ) {
        $this->baseUrl = "https://graph.facebook.com/{$graphVersion}";
    }

    public static function fromConfig(): self
    {
        return new self(
            appId: config('services.facebook.client_id') ?? '',
            appSecret: config('services.facebook.client_secret') ?? '',
            graphVersion: config('services.facebook.graph_version', 'v19.0'),
        );
    }

    /**
     * El token que devuelve el flujo OAuth estándar dura ~2hs — se
     * canjea una vez por uno de larga duración (~60 días) para no pedirle
     * al tenant que reconecte la cuenta cada dos horas.
     */
    public function intercambiarPorTokenDeLargaDuracion(string $tokenCorto): array
    {
        $respuesta = Http::get("{$this->baseUrl}/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $tokenCorto,
        ]);

        $respuesta->throw();

        return [
            'access_token' => $respuesta->json('access_token'),
            'expira_en' => now()->addSeconds((int) $respuesta->json('expires_in', 5184000)),
        ];
    }

    /**
     * Páginas que administra el usuario que autorizó — cada una con su
     * propio Page Access Token, distinto del token de usuario. Es ese
     * token de Página el que hay que guardar en CuentaConectada, no el
     * de usuario (§09: "conecta su página de Facebook").
     */
    public function listarPaginas(string $tokenDeUsuario): array
    {
        $respuesta = Http::get("{$this->baseUrl}/me/accounts", [
            'access_token' => $tokenDeUsuario,
            'fields' => 'id,name,access_token',
        ]);

        $respuesta->throw();

        return $respuesta->json('data', []);
    }

    /**
     * Instagram no tiene login propio acá — se llega a una cuenta
     * profesional exclusivamente a través de la Página de Facebook a la
     * que está vinculada (§09: "vinculada a la página"). Null si esa
     * Página no tiene ninguna cuenta de Instagram conectada.
     */
    public function cuentaInstagramVinculada(string $idPagina, string $tokenDePagina): ?array
    {
        $respuesta = Http::get("{$this->baseUrl}/{$idPagina}", [
            'access_token' => $tokenDePagina,
            'fields' => 'instagram_business_account{id,username}',
        ]);

        $respuesta->throw();

        $cuenta = $respuesta->json('instagram_business_account');

        return $cuenta ? ['id' => $cuenta['id'], 'username' => $cuenta['username'] ?? null] : null;
    }

    public function tokenValido(string $token): bool
    {
        try {
            $respuesta = Http::get("{$this->baseUrl}/me", ['access_token' => $token]);

            return $respuesta->successful();
        } catch (Throwable) {
            return false;
        }
    }
}
