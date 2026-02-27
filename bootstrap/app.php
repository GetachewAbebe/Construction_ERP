<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: __DIR__.'/../routes/health.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /**
         * Global middleware (runs on every request)
         *
         * If you have any global middleware, you can register them like:
         *
         * $middleware->use([
         *     // \App\Http\Middleware\SomeGlobalMiddleware::class,
         * ]);
         */

        /**
         * Route middleware aliases
         *
         * These names are used in your routes:
         *   ->middleware('auth')
         *   ->middleware('guest')
         *   ->middleware('role:Administrator')
         */
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'role' => RoleMiddleware::class,
            'prevent-back-history' => PreventBackHistory::class,

            // You can add more aliases here later if needed, e.g.:
            // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling here if needed
    })
    // 🔥 THIS IS THE IMPORTANT PART: without this, you return ApplicationBuilder
    ->create();
