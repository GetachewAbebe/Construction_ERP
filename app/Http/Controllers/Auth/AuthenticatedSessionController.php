<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * TEMP: After successful login, force-redirect to /test-login
     * so we can prove the session persists. Once confirmed,
     * switch the final redirect back to role-based dashboards.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        Log::info('LOGIN_STORE_REACHED', ['email' => $request->input('email')]);

        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        if (method_exists($user, 'getRoleNames')) {
            Log::info('LOGIN_ROLES', [
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]);
        }

        // ✅ TEMPORARY: prove session/cookie survives
        return redirect('/test-login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
