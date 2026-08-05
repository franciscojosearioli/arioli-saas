<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotClienteAuthenticated
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::guard('cliente')->check()) {
            return redirect()->route('cliente.login');
        }

        // Set default guard to 'cliente' so Gate::authorize() resolves
        // the correct user instead of falling back to the 'web' guard.
        Auth::shouldUse('cliente');

        return $next($request);
    }
}