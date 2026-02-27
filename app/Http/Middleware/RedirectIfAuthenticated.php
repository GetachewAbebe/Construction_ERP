<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            $target = $this->dashboardPathForRole($user?->role);

            return redirect()->intended($target);
        }

        return $next($request);
    }

    /**
     * Decide which dashboard URL to send the user to, based on their role.
     */
    protected function dashboardPathForRole(?string $rawRole): string
    {
        if ($rawRole === null || $rawRole === '') {
            return route('home');
        }

        $normalized = strtolower(trim($rawRole));

        return match ($normalized) {
            'administrator' => route('admin.dashboard'),
            'humanresourcemanager' => route('hr.dashboard'),
            'inventorymanager' => route('inventory.dashboard'),
            'financialmanager' => route('finance.dashboard'),
            default => route('home'),
        };
    }
}
