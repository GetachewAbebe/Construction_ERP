<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('system_global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        Gate::before(function ($user, $ability) {
            return ($user->hasRole('Administrator') || $user->hasRole('Admin')) ? true : null;
        });

        Gate::define('manage-attendance', function ($user) {
            return $user->hasRole('HumanResourceManager') ||
                   $user->hasRole('Human Resource Manager');
        });
    }
}
