<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('cliente.login');
    }

    /**
     * Destino de la URL firmada que arma Admin\ClientController::impersonate().
     * El login pasa acá (request real recibido por cliente.arioli.dev) para
     * que la cookie de sesión del guard 'cliente' quede seteada en el
     * dominio correcto — no se puede loguear del lado de admin.arioli.dev y
     * redirigir, son dominios distintos con cookies independientes.
     */
    public function enterAsImpersonated(Request $request, User $user)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($user->client_id, 404);

        Auth::guard('cliente')->login($user);

        return redirect()->route('cliente.dashboard');
    }

    public function login(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required|string',
        ];

        if (config('services.turnstile.sitekey') && config('services.turnstile.secret')) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $validated = $request->validate($rules);

        if (config('services.turnstile.sitekey') && config('services.turnstile.secret')) {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if (!($response->json('success') ?? false)) {
                return back()->withErrors([
                    'cf-turnstile-response' => 'La verificación de seguridad falló, intente nuevamente.',
                ])->withInput();
            }
        }

        $user = User::where(fn ($q) => $q->whereNotNull('tenant_id')->orWhereNotNull('client_id'))
            ->where('email', $validated['email'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Las credenciales no son correctas.',
            ])->withInput();
        }

        Auth::guard('cliente')->login($user, $request->boolean('remember'));

        return redirect()->route('cliente.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cliente.login');
    }

    public function showForgotPassword()
    {
        return view('cliente.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('clientes')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Te enviamos el link de recuperación a tu email.')
            : back()->withErrors(['email' => 'No encontramos ese email en nuestros registros.']);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('cliente.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('clientes')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'       => bcrypt($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('cliente.login')->with('status', 'Contraseña actualizada correctamente.')
        : back()->withErrors(['email' => 'El link es inválido o expiró.']);
}
}