<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PublicApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Public-Token');
        if ($token !== config('app.public_api_token')) {
            return response()->json(['error' => 'Unauthorized public access'], 401);
        }

        return $next($request);
    }
}
