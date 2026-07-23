<?php

namespace App\Http\Middleware;

use App\Models\LoginHistory;
use Closure;
use Illuminate\Http\Request;

class TrackLastActivity
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        // Only track authenticated admins, skip AJAX/API pings to avoid noise
        if (auth()->check() && auth()->user()->is_admin) {
            $sessionId = $request->session()->getId();

            LoginHistory::where('session_id', $sessionId)
                ->whereNull('logout_at')
                ->update(['last_activity_at' => now()]);
        }

        return $response;
    }
}
