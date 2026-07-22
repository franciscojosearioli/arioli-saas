<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('saas.api_token');

        if (!$secret) {
            return response()->json(['error' => 'API not configured'], 500);
        }

        $token = $request->bearerToken();

        if (!$token || !hash_equals($secret, $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
