<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jumbojett\OpenIDConnectClient;

class OpenIDConnectController extends Controller
{
    public function redirectToProvider()
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_PROVIDER_URL'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        $oidc->setRedirectURL(env('OIDC_REDIRECT_URI'));
        $oidc->authenticate();
    }

    public function handleProviderCallback(Request $request)
    {
        $oidc = new OpenIDConnectClient(
            env('OIDC_PROVIDER_URL'),
            env('OIDC_CLIENT_ID'),
            env('OIDC_CLIENT_SECRET')
        );

        $oidc->setRedirectURL(env('OIDC_REDIRECT_URI'));
        $oidc->authenticate();

        $userInfo = $oidc->requestUserInfo();

        // Manejar la autenticación del usuario en tu aplicación
        // Por ejemplo, puedes buscar o crear un usuario en tu base de datos y luego iniciar sesión

        // $authUser = User::firstOrCreate([...]);
        // Auth::login($authUser);

        return redirect(auth()->user()?->is_admin ? '/admin/dashboard' : '/dashboard');
    }
}