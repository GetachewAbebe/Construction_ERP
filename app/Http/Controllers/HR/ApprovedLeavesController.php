<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeOnLeave;
use Illuminate\Http\Request;

class ApprovedLeavesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $approved = EmployeeOnLeave::with('employee', 'approver')
            ->latest('approved_at')
            ->paginate(20);

        return view('hr.leaves.approved', compact('approved'));
    }
}
