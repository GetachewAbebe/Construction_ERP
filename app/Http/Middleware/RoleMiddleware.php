<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage in routes:
     *  ->middleware('role:Administrator')
     *  ->middleware('role:Administrator,HumanResourceManager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        // 1. Spatie Roles are the primary source of truth
        foreach ($roles as $role) {
            // Check both Spatie hasRole() AND the custom role column (normalized)
            if ($user->hasRole($role) || strtolower($user->role) === str_replace(' ', '', strtolower($role))) {
                return $next($request);
            }
        }

        // 2. Logic to keep the 'role' column in sync with Spatie roles if missing
        if (empty($user->role) && $user->roles->count() > 0) {
            $user->role = $user->roles->first()->name;
            $user->save();
            
            // Re-check after sync
            foreach ($roles as $role) {
                if (strtolower($user->role) === str_replace(' ', '', strtolower($role))) {
                    return $next($request);
                }
            }
        }

        // 3. Fallback for Administrator-only routes
        if (count($roles) === 1 && strtolower($roles[0]) === 'administrator') {
            abort(403, 'Only Administrators can access this page.');
        }

        abort(403, "Access denied. Required roles: " . implode(', ', $roles));
    }
}
