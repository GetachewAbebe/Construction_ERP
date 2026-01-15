<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // If user is already logged in, send them straight to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $rawRole = $user->role;

            if ($user->hasRole('Administrator')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('Human Resource Manager')) {
                return redirect()->route('hr.dashboard');
            } elseif ($user->hasRole('Inventory Manager')) {
                return redirect()->route('inventory.dashboard');
            } elseif ($user->hasRole('Financial Manager')) {
                return redirect()->route('finance.dashboard');
            }

            // Fallback check
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

            // Unknown / unmapped role → block access clearly
            abort(
                403,
                "Unknown or unmapped role '{$rawRole}'. Please check the user's role assignment."
            );
        }

        // Guest: show the login form
        return view('home');
    }
}
