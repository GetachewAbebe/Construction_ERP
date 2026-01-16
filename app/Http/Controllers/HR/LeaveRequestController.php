<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\LeaveRequestStatusNotification;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'active');

        if ($view === 'logs') {
            $approved = \App\Models\EmployeeOnLeave::with('employee', 'approver')
                ->latest('approved_at')
                ->paginate(20)
                ->withQueryString();
            
            return view('hr.leaves.index', compact('approved', 'view'));
        }

        $query = LeaveRequest::with('employee');

        // Search Scope
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount  = LeaveRequest::where('status', 'Pending')->count();
        $approvedCount = LeaveRequest::where('status', 'Approved')->count();
        $rejectedCount = LeaveRequest::where('status', 'Rejected')->count();
        
        return view('hr.leaves.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount', 'view'));
    }

    public function create()
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id','first_name','last_name']);
        return view('hr.leaves.create', compact('employees'));
    }

    public function store(\App\Http\Requests\HR\StoreLeaveRequest $request)
    {
        $data = $request->validated();

        try {
            $leaveRequest = LeaveRequest::create($data);

            // Notify Administrators
            // Notify Administrators
            // Default to 'Administrator' role, but fallback to first user if none found (for dev/testing safety)
            $admins = User::role('Administrator')->get();
            if ($admins->isEmpty()) {
                // Determine a fallback admin (e.g., ID 1 or a specific email)
                $admins = User::where('id', 1)->get();
            }

            try {
                Notification::send($admins, new LeaveRequestStatusNotification($leaveRequest, 'request'));
            } catch (\Exception $e) {
                // Log detailed error but don't stop the request
                \Log::error('Leave request notification dispatch failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            }

            return redirect()->route('hr.leaves.index')
                ->with('success', 'Leave request successfully filed and queued for administrative review.');

        } catch (\Exception $e) {
            \Log::error('Leave request recording failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to process leave request. Please check system logs.');
        }
    }

    public function getLeaveDates(Employee $employee)
    {
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->select('start_date', 'end_date', 'status')
            ->get()
            ->map(function ($l) {
                return [
                    'start' => $l->start_date->toDateString(),
                    'end' => $l->end_date->toDateString(),
                    'status' => $l->status
                ];
            });

        return response()->json($leaves);
    }
    public function show(LeaveRequest $leave)
    {
        $leave->load('employee');
        return view('hr.leaves.show', compact('leave'));
    }
}
