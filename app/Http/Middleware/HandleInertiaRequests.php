<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    'dashboardRoute' => $user->getDashboardRouteName(),
                ] : null,
            ],
            'counts' => fn () => $user ? [
                'pendingExpenses' => \App\Models\Expense::whereIn('status', ['pending', 'Pending', \App\Enums\ExpenseStatus::Pending->value])->count(),
                'pendingLoans' => \App\Models\InventoryLoan::whereIn('status', ['pending', 'Pending', \App\Enums\LoanStatus::Pending->value])->count(),
                'pendingLeaves' => \App\Models\LeaveRequest::whereIn('status', ['Pending', 'pending', \App\Enums\LeaveStatus::Pending->value])->count(),
                'unreadNotifications' => $user->unreadNotifications()->count(),
            ] : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
                'message' => fn () => $request->session()->get('message'),
            ],
        ];
    }
}
