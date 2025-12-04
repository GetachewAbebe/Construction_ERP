<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * These must match the "role" values used in:
     *  - RoleMiddleware
     *  - web.php redirects
     */
    private array $allowedRoles = [
        'Administrator',
        'HumanResourceManager',
        'InventoryManager',
        'FinancialManager',
    ];

    /**
     * (Optional) Extra safety so only Administrator can manage users,
     * even though web.php already uses middleware('role:Administrator').
     */
    private function enforceAdmin(): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $rawRole  = $user->role ?? '';
        $roleSlug = strtolower(trim((string) $rawRole));

        if ($roleSlug !== 'administrator') {
            abort(403, 'Only Administrators can access this page.');
        }
    }

    /**
     * List users with optional search + pagination.
     * GET /admin/users
     */
    public function index(Request $request): View
    {
        $this->enforceAdmin();

        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                // Postgres-friendly case-insensitive search
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'ILIKE', "%{$q}%")
                        ->orWhere('email', 'ILIKE', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = $this->allowedRoles;

        return view('admin.users.index', compact('users', 'q', 'roles'));
    }

    /**
     * Show create form.
     * GET /admin/users/create
     */
    public function create(): View
    {
        $this->enforceAdmin();

        $roles = $this->allowedRoles;

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store new user.
     * POST /admin/users
     */
    public function store(Request $request): RedirectResponse
    {
        $this->enforceAdmin();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', Rule::in($this->allowedRoles)],
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],   // 👈 important
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User {$user->name} registered successfully.");
    }

    /**
     * Show edit form.
     * GET /admin/users/{user}/edit
     */
    public function edit(User $user): View
    {
        $this->enforceAdmin();

        $roles        = $this->allowedRoles;
        $currentRole  = $user->role;   // simple column value

        return view('admin.users.edit', compact('user', 'roles', 'currentRole'));
    }

    /**
     * Update user. Password is optional.
     * PUT /admin/users/{user}
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->enforceAdmin();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role'     => ['required', Rule::in($this->allowedRoles)],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->role  = $validated['role'];  // 👈 keep column in sync

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User {$user->name} updated successfully.");
    }

    /**
     * Delete user (cannot delete yourself).
     * DELETE /admin/users/{user}
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->enforceAdmin();

        if (Auth::id() === $user->id) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User {$name} deleted.");
    }
}
