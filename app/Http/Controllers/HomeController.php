<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // If user is already logged in, send them straight to their dashboard
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRouteName());
        }

        // Guest: show the React Inertia login form
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }
}
