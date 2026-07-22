<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PreventConcurrentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();
        $routeName = $request->route()->getName();
        
        // Check if another user is viewing this route
        $currentViewer = Cache::get('viewing_route_' . $routeName);
        
        if ($currentViewer && $currentViewer != $userId) {
            // Another user is viewing this route, notify the user
            return redirect()->back();
        }
        
        // Set the current user as the viewer of this route
        Cache::put('viewing_route_' . $routeName, $userId, now()->addMinutes(5));

        $response = $next($request);

        // Remove the current user from the route viewer list on response
        Cache::forget('viewing_route_' . $routeName);

        return $response;
    }
}
