<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeOnLeave;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveApprovalController extends Controller
{
    public function index()
    {
        $pending = LeaveRequest::with('employee')
            ->where('status','Pending')
            ->orderByDesc('id')
            ->paginate(20);

        $approved = EmployeeOnLeave::with('employee','approver')
            ->orderByDesc('approved_at')
            ->paginate(20);

        return view('admin.requests.leave-approvals', compact('pending','approved'));
    }

    public function approve(LeaveRequest $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('status','This request is already processed.');
        }

        DB::transaction(function () use ($leave) {
            $leave->update([
                'status'      => 'Approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            EmployeeOnLeave::create([
                'employee_id' => $leave->employee_id,
                'start_date'  => $leave->start_date,
                'end_date'    => $leave->end_date,
                'reason'      => $leave->reason,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('status','Leave approved and recorded.');
    }

    public function reject(LeaveRequest $leave)
    {
        if ($leave->status !== 'Pending') {
            return back()->with('status','This request is already processed.');
        }

        $leave->update([
            'status'      => 'Rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('status','Leave rejected.');
    }
}
