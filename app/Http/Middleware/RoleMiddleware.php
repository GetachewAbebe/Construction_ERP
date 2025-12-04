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

        if (! $user) {
            abort(403, 'Unauthorized (no authenticated user).');
        }

        // 1. Try to read the role from DB
        $rawRole = $user->role ?? null;

        // 2. If role is missing, try infer from email ONCE and persist
        if ($rawRole === null || $rawRole === '') {
            $emailToRole = [
                'administrator@natanemengineering.com'    => 'Administrator',
                'inventorymanager@natanemengineering.com' => 'InventoryManager',
                'humanresource@natanemengineering.com'    => 'HumanResourceManager',
                'financialmanager@natanemengineering.com' => 'FinancialManager',
            ];

            $userEmail = strtolower(trim((string) $user->email));

            if (isset($emailToRole[$userEmail])) {
                $rawRole    = $emailToRole[$userEmail];
                $user->role = $rawRole;
                $user->save();
            }
        }

        // 3. Still empty? Then this account effectively has no role
        if ($rawRole === null || $rawRole === '') {
            abort(403, 'This account does not have a role set.');
        }

        // 4. Normalize user role (remove spaces, case, etc.)
        $normalized = strtolower(trim((string) $rawRole));

        // Map common variants to canonical internal slugs
        $map = [
            'administrator'        => 'administrator',
            'admin'                => 'administrator',          // common short form
            'systemadmin'          => 'administrator',          // just in case
            'humanresourcemanager' => 'humanresourcemanager',
            'hr'                   => 'humanresourcemanager',
            'inventorymanager'     => 'inventorymanager',
            'inventory'            => 'inventorymanager',
            'financialmanager'     => 'financialmanager',
            'finance'              => 'financialmanager',
        ];

        // Start with direct map if possible
        $userRoleSlug = $map[$normalized] ?? $normalized;

        // Extra safety: any role string containing 'admin' becomes Administrator
        // (but "humanresourcemanager" does NOT contain 'admin' as a substring)
        if (! isset($map[$normalized]) && str_contains($normalized, 'admin')) {
            $userRoleSlug = 'administrator';
        }

        // 5. Normalize allowed roles from middleware argument(s)
        $allowedRoleSlugs = array_map(function ($r) use ($map) {
            $key = strtolower(trim((string) $r));
            return $map[$key] ?? $key;
        }, $roles);

        // 6. Final permission check
        if (in_array($userRoleSlug, $allowedRoleSlugs, true)) {
            return $next($request);
        }

        // 7. Keep your old nice message for Administrator-only routes
        if (count($roles) === 1 && strtolower(trim((string) $roles[0])) === 'administrator') {
            abort(403, 'Only Administrators can access this page.');
        }

        abort(
            403,
            "You do not have permission to access this resource. "
            . "User role is '{$rawRole}', allowed: " . implode(', ', $roles)
        );
    }
}
