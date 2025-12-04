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
        $rawRole = $user->role ?? null;
        // Normalize: lowercase AND remove spaces
        // e.g. "Human Resource Manager" -> "humanresourcemanager"
        $role    = $rawRole ? strtolower(str_replace(' ', '', $rawRole)) : null;

        // Redirect based on your 4 roles
        if ($role === 'administrator') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'humanresourcemanager') {
            return redirect()->route('hr.dashboard');
        } elseif ($role === 'inventorymanager') {
            return redirect()->route('inventory.dashboard');
        } elseif ($role === 'financialmanager') {
            return redirect()->route('finance.dashboard');
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
