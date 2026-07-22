<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! auth()->user()) {
                    return redirect()->route('login'); // Redirigir al login
        }
        if (! auth()->user()->is_admin) {
            abort(403);
        }

        return $next($request);
    }
}