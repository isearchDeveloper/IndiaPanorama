<?php

namespace App\Http\Middleware;

use App\Services\AdminPermissions;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->is_super_admin) return $next($request);

        $routeName = $request->route()?->getName();
        if (!$routeName) return $next($request);

        $required = AdminPermissions::resolveForRoute($routeName);
        if (!$required) return $next($request);

        if (!$user->hasPermission($required)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. You do not have access to this section.'], 403);
            }
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
