<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Bootstrap 5 styles for pagination links
        Paginator::useBootstrapFive();

        // --- PRODUCTION HARDENING: Rate Limiters ---
        
        // 1. Global API Rate Limiter
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // 2. Brute-force Protection for Auth (Login, Reset Password)
        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // 3. Global Protection for Web Traffic
        \Illuminate\Support\Facades\RateLimiter::for('system_global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->ip());
        });

        // --- ACCESS CONTROL: Gates ---
        
        // Super Admin Bypass
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('Administrator') || $user->hasRole('Admin')) ? true : null;
        });

        // Attendance Management
        Gate::define('manage-attendance', function ($user) {
            return $user->hasRole('HumanResourceManager') || 
                   $user->hasRole('Human Resource Manager');
        });
    }
}
