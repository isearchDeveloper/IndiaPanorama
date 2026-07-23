<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 🔹 Route middleware aliases
        $middleware->alias([
            'auth'              => \App\Http\Middleware\Authenticate::class,
            'isadmin'           => IsAdmin::class,
            'checkpermission'   => \App\Http\Middleware\CheckPermission::class,
            'public.token'      => \App\Http\Middleware\PublicApiToken::class,
        ]);

        // 🔹 Global web middleware (sessions, cookies, errors, CSRF)
        $middleware->web(prepend: [
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // 🔹 API middleware group
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Not Found'], 404);
                }

                return response()->view('errors.404', [], 404);
            }

            return null;
        });
    })
    ->create();
