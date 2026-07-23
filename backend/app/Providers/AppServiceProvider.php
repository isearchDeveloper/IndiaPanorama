<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Models\Package;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register rate limiter for 'api'.
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
    }
}
