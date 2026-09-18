<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile overview.
     */
    public function show(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->loadMissing(['employee', 'roles', 'permissions']);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role ?? ($user->getRoleNames()->first() ?? 'Administrator'),
            'roles' => $user->getRoleNames(),
            'position' => $user->position ?? ($user->employee?->position ?? 'Authorized Personnel'),
            'department' => $user->department ?? ($user->employee?->department ?? 'Operations'),
            'status' => $user->status ?? 'Active',
            'created_at' => $user->created_at ? $user->created_at->format('M d, Y') : null,
            'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name')->values()->all() : [],
            'employee' => $user->employee ? [
                'id' => $user->employee->id,
                'first_name' => $user->employee->first_name,
                'last_name' => $user->employee->last_name,
                'department' => $user->employee->department,
                'position' => $user->employee->position,
                'hire_date' => $user->employee->hire_date ? \Carbon\Carbon::parse($user->employee->hire_date)->format('M d, Y') : null,
                'profile_picture' => $user->employee->profile_picture,
                'profile_picture_url' => $user->employee->profile_picture_url,
            ] : null,
            'profile_picture_url' => $user->employee?->profile_picture_url,
        ];

        return Inertia::render('Profile/Show', [
            'user' => $userData,
            'status' => session('status'),
        ]);
    }

    /**
     * Show the profile edit form.
     */
    public function edit(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->loadMissing(['employee', 'roles']);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role ?? ($user->getRoleNames()->first() ?? 'Administrator'),
            'roles' => $user->getRoleNames(),
            'position' => $user->position,
            'department' => $user->department,
            'status' => $user->status ?? 'Active',
            'employee' => $user->employee ? [
                'id' => $user->employee->id,
                'profile_picture_url' => $user->employee->profile_picture_url,
            ] : null,
            'profile_picture_url' => $user->employee?->profile_picture_url,
        ];

        return Inertia::render('Profile/Edit', [
            'user' => $userData,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle Name Parts
        $parts = explode(' ', trim($request->name));
        $user->first_name = array_shift($parts);
        $user->last_name = array_pop($parts) ?: '';
        $user->middle_name = implode(' ', $parts);

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Unified Identity Synchronization (Ensure link exists)
        $employee = $user->employee;

        // Auto-repair link if it's missing (e.g. from legacy import or case mismatch)
        if (! $employee) {
            $employee = \App\Models\Employee::where('email', $user->email)->first();
            if ($employee) {
                $employee->update(['user_id' => $user->id]);
            }
        }

        // Handle Profile Picture
        if ($request->hasFile('profile_picture')) {
            if ($employee) {
                // Delete old picture if it exists
                if ($employee->profile_picture && Storage::disk('public')->exists($employee->profile_picture)) {
                    Storage::disk('public')->delete($employee->profile_picture);
                }

                $path = $request->file('profile_picture')->store('employees', 'public');
                // Ensure forward slashes for cross-platform compatibility
                $standardPath = str_replace('\\', '/', $path);
                $employee->update(['profile_picture' => $standardPath]);
            }
        }

        $targetRoute = $user->getProfileRouteName('show');
        if (! Route::has($targetRoute)) {
            $targetRoute = 'admin.profile.show';
        }

        return redirect()->route($targetRoute)->with('success', 'Profile updated successfully.');
    }
}

