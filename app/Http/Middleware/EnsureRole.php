<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Require the authenticated user to have at least one of the given roles.
     * Usage in routes: EnsureRole::class.':Administrator'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401); // 'auth' middleware should redirect if not logged in
        }

        if (! method_exists($user, 'hasAnyRole')) {
            abort(403, 'Roles not configured on user model.');
        }

        if (! $user->hasAnyRole($roles)) {
            abort(403, 'You do not have the required role.');
        }

        return $next($request);
    }
}
