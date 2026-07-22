<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCodeNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        notify()->preset('login-success');
        return auth()->user()->is_admin ? '/admin/dashboard' : '/dashboard';
    }

protected function authenticated(Request $request, $user)
{
    if ($user->two_factor) {
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCodeNotification());
    }
}

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return redirect()->back()
            ->withInput($request->only($this->username()))
            ->withErrors(['email' => trans('auth.throttle', ['seconds' => $seconds])]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())).'|'.$request->ip();
    }

public function login(Request $request)
{
    $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    // Solo validar Turnstile si está configurado
    if (config('services.turnstile.site_key') && config('services.turnstile.secret_key')) {
        $rules['cf-turnstile-response'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Validar con Cloudflare Turnstile solo si está configurado
    if (config('services.turnstile.site_key') && config('services.turnstile.secret_key')) {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        if (!($result['success'] ?? false)) {
            return redirect()->back()
                ->withErrors(['cf-turnstile-response' => 'La verificación de seguridad falló, intente nuevamente.'])
                ->withInput();
        }
    }

    // Si pasa el captcha (o no se requiere), sigue login normal
    if (auth()->attempt($request->only('email', 'password'), $request->filled('remember'))) {
        return redirect($this->redirectTo());
    }

    return redirect()->back()->with('message', 'Las credenciales no coinciden con nuestros registros.');
}

public function logout(Request $request)
{
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();


    return redirect()->route('login')->with('message', 'Por favor, inicia sesion nuevamente.');
}
}