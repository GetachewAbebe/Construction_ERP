<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile overview.
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle Name Parts
        $parts = explode(' ', $request->name);
        $user->first_name = array_shift($parts);
        $user->last_name = array_pop($parts) ?: '';
        $user->middle_name = implode(' ', $parts);

        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        // $user->bio = $request->bio; // Disabled: Column missing in production schema

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

        return redirect()->route($user->getProfileRouteName('show'))->with('success', 'Profile identity updated successfully.');
    }
}
