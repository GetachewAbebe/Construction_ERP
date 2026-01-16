<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleAuthController extends Controller
{
    /**
     * Handle login POST (from "/").
     */
    public function login(Request $request)
    {
        // Change this to 'username' if you log in with username
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user    = Auth::user();
        $rawRole = $user->role; // Get the role from the 'role' column in users table

        // Redirect based on roles using Spatie's hasRole
        if ($user->hasRole('Administrator') || $user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('Human Resource Manager')) {
            return redirect()->route('hr.dashboard');
        } elseif ($user->hasRole('Inventory Manager')) {
            return redirect()->route('inventory.dashboard');
        } elseif ($user->hasRole('Financial Manager')) {
            return redirect()->route('finance.dashboard');
        }

        // Fallback: If Spatie hasRole fails, try checking the 'role' column (for backward compatibility or misconfiguration)
        $roleMap = [
            'Administrator'           => 'admin.dashboard',
            'administrator'           => 'admin.dashboard',
            'Human Resource Manager'  => 'hr.dashboard',
            'HumanResourceManager'    => 'hr.dashboard',
            'Inventory Manager'       => 'inventory.dashboard',
            'InventoryManager'        => 'inventory.dashboard',
            'Financial Manager'       => 'finance.dashboard',
            'FinancialManager'        => 'finance.dashboard',
        ];

        if (isset($roleMap[$rawRole])) {
            return redirect()->route($roleMap[$rawRole]);
        }

        // Unknown role → show a clear error
        abort(
            403,
            "Unknown or unmapped role '{$rawRole}'. Please check the user's role value in the database."
        );
    }

    /**
     * Handle logout POST.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Back to "/" (your login page)
        return redirect()->route('home');
    }
}
