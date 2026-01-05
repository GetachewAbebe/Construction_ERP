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
                    $sub->where('first_name', 'ILIKE', "%{$q}%")
                        ->orWhere('middle_name', 'ILIKE', "%{$q}%")
                        ->orWhere('last_name', 'ILIKE', "%{$q}%")
                        ->orWhere('email', 'ILIKE', "%{$q}%");
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
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
            'first_name'   => ['required', 'string', 'max:50'],
            'middle_name'  => ['nullable', 'string', 'max:50'],
            'last_name'    => ['required', 'string', 'max:50'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', Password::min(8)],
            'role'         => ['required', Rule::in($this->allowedRoles)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'position'     => ['nullable', 'string', 'max:100'],
            'department'   => ['nullable', 'string', 'max:100'],
            'status'       => ['nullable', 'string', 'in:Active,Inactive,Suspended'],
            'bio'          => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::create([
            'first_name'        => $validated['first_name'],
            'middle_name'       => $validated['middle_name'] ?? null,
            'last_name'         => $validated['last_name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'phone_number'      => $validated['phone_number'] ?? null,
            'position'          => $validated['position'] ?? null,
            'department'        => $validated['department'] ?? null,
            'status'            => $validated['status'] ?? 'Active',
            'bio'               => $validated['bio'] ?? null,
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
            'first_name'   => ['required', 'string', 'max:50'],
            'middle_name'  => ['nullable', 'string', 'max:50'],
            'last_name'    => ['required', 'string', 'max:50'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', Password::min(8)],
            'role'         => ['required', Rule::in($this->allowedRoles)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'position'     => ['nullable', 'string', 'max:100'],
            'department'   => ['nullable', 'string', 'max:100'],
            'status'       => ['nullable', 'string', 'in:Active,Inactive,Suspended'],
            'bio'          => ['nullable', 'string', 'max:1000'],
        ]);

        $user->first_name   = $validated['first_name'];
        $user->middle_name  = $validated['middle_name'] ?? null;
        $user->last_name    = $validated['last_name'];
        $user->email        = $validated['email'];
        $user->role         = $validated['role'];
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->position     = $validated['position'] ?? null;
        $user->department   = $validated['department'] ?? null;
        $user->status       = $validated['status'] ?? 'Active';
        $user->bio          = $validated['bio'] ?? null;

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
