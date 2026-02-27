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
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Centralized redirection logic from User model
        $routeName = $user->getDashboardRouteName();

        if ($routeName === 'home') {
            Auth::logout();
            abort(403, "Unknown or unmapped role '{$user->role}'. Please check the user's role in the database.");
        }

        return redirect()->route($routeName);
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
