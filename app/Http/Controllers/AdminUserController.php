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
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::with('employee')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('first_name', 'ILIKE', "%{$q}%")
                        ->orWhere('middle_name', 'ILIKE', "%{$q}%")
                        ->orWhere('last_name', 'ILIKE', "%{$q}%")
                        ->orWhere('email', 'ILIKE', "%{$q}%");
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        $roles = $this->allowedRoles;

        return view('admin.users.index', compact('users', 'q', 'roles'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $roles = $this->allowedRoles;
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store new user.
     */
    public function store(\App\Http\Requests\Admin\StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $parts = explode(' ', $validated['name']);
        $firstName = array_shift($parts);
        $lastName  = array_pop($parts);
        $middleName = implode(' ', $parts);

        $user = User::create([
            'first_name'        => $firstName,
            'middle_name'       => $middleName,
            'last_name'         => $lastName ?: 'User',
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'phone_number'      => $request->phone_number,
            'position'          => $request->position,
            'department'        => $request->department,
            'status'            => $request->status ?? 'Active',
            'email_verified_at' => now(),
        ]);

        // Sync role via Spatie if applicable
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($validated['role']);
        }

        // Handle Profile Picture
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('employees', 'public');
        }

        // Auto-create Employee Record
        \App\Models\Employee::create([
            'user_id'         => $user->id,
            'first_name'      => $firstName,
            'last_name'       => $lastName ?: 'User',
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
            ->with('success', "User identity for {$user->name} has been successfully established.");
    }

    /**
     * Show edit form.
     */
    public function edit(User $user): View
    {
        $roles        = $this->allowedRoles;
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user.
     */
    public function update(\App\Http\Requests\Admin\UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $parts = explode(' ', $validated['name']);
        $user->first_name = array_shift($parts);
        $user->last_name  = array_pop($parts);
        $user->middle_name = implode(' ', $parts);

        $user->email       = $validated['email'];
        $user->role        = $validated['role'];
        $user->phone_number = $request->phone_number;
        $user->position     = $request->position;
        $user->department   = $request->department;
        $user->status       = $request->status ?? 'Active';
        $user->bio          = $request->bio;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync role via Spatie
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }

        // Handle Profile Picture
        $profilePath = optional($user->employee)->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($profilePath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profilePath);
            }
            $profilePath = $request->file('profile_picture')->store('employees', 'public');
        }

        // Sync to Employee (Create if missing)
        \App\Models\Employee::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name ?: 'User',
                'email'           => $user->email,
                'status'          => $user->status,
                'phone'           => $user->phone_number,
                'position'        => $user->position,
                'department'      => $user->department,
                'profile_picture' => $profilePath,
                'hire_date'       => now(), // Default for auto-created records
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Operational credentials for {$user->name} have been updated.");
    }

    /**
     * Delete user safely.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Critical Error: Self-termination of administrative session is prohibited.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Account identity for {$name} has been expunged from active records.");
    }

}
