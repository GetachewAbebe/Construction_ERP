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

        $users = User::with('employee')
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
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', Password::min(8)],
            'role'            => ['required', Rule::in($this->allowedRoles)],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // Added validation
            // Add other optional fields validation if needed for employee data
            'phone_number'    => ['nullable', 'string', 'max:20'],
            'position'        => ['nullable', 'string', 'max:255'],
            'department'      => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'in:Active,Inactive,Suspended'],
        ]);

        $parts = explode(' ', $validated['name']);
        $firstName = array_shift($parts);
        $lastName  = array_pop($parts);
        $middleName = implode(' ', $parts);

        $user = User::create([
            'first_name'        => $firstName,
            'middle_name'       => $middleName,
            'last_name'         => $lastName,
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'phone_number'      => $request->phone_number,
            'position'          => $request->position,
            'department'        => $request->department,
            'status'            => $request->status ?? 'Active',
            'bio'               => $request->bio,
            'email_verified_at' => now(),
        ]);

        // Handle Profile Picture
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('employees', 'public');
        }

        // Auto-create Employee Record (Mirroring data)
        \App\Models\Employee::create([
            'user_id'         => $user->id,
            'first_name'      => $firstName,
            'last_name'       => $lastName ? $lastName : 'User',
            'email'           => $user->email,
            'status'          => $request->status ?? 'Active',
            'hire_date'       => now(),
            'profile_picture' => $profilePath,
            'phone'           => $request->phone_number,
            'position'        => $request->position,
            'department'      => $request->department,
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
            'name' => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', Password::min(8)],
            'role'     => ['required', Rule::in($this->allowedRoles)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'position'     => ['nullable', 'string', 'max:255'],
            'department'   => ['nullable', 'string', 'max:255'],
            'status'       => ['nullable', 'in:Active,Inactive,Suspended'],
            'bio'          => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // Added validation
        ]);

        // Name handling
        $parts = explode(' ', $validated['name']);
        $user->first_name = array_shift($parts);
        $user->last_name  = array_pop($parts);
        $user->middle_name = implode(' ', $parts);

        $user->email      = $validated['email'];
        $user->role       = $validated['role'];
        $user->phone_number = $request->phone_number;
        $user->position     = $request->position;
        $user->department   = $request->department;
        $user->status       = $request->status ?? 'Active';
        $user->bio          = $request->bio;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Handle Profile Picture
        $profilePath = optional($user->employee)->profile_picture;
        if ($request->hasFile('profile_picture')) {
            // Delete old if exists (optional but recommended)
            if ($profilePath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profilePath);
            }
            $profilePath = $request->file('profile_picture')->store('employees', 'public');
        }

        // Sync to Employee if exists
        $employee = $user->employee;
        if($employee) {
            $employee->update([
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name ?: 'User',
                'email'           => $user->email,
                'status'          => $user->status,
                'phone'           => $user->phone_number,
                'position'        => $user->position,
                'department'      => $user->department,
                'profile_picture' => $profilePath,
            ]);
        }

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
