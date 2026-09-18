<?php

declare(strict_types=1);

namespace App\Http\Controllers\HR;

use App\Enums\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Mail\NewLeaveRequestMail;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

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

            return \Inertia\Inertia::render('HR/Leaves/Index', [
                'approved' => $approved,
                'view' => $view,
                'q' => $request->q ?? '',
                'status' => $request->status ?? '',
            ]);
        }

        $query = LeaveRequest::with('employee');

        // Search Scope
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('employee', function ($q) use ($search) {
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

        $pendingCount = LeaveRequest::where('status', LeaveStatus::Pending->value)->count();
        $approvedCount = LeaveRequest::where('status', LeaveStatus::Approved->value)->count();
        $rejectedCount = LeaveRequest::where('status', LeaveStatus::Rejected->value)->count();

        return \Inertia\Inertia::render('HR/Leaves/Index', [
            'requests' => $requests,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'view' => $view,
            'q' => $request->q ?? '',
            'status' => $request->status ?? '',
        ]);
    }

    public function create()
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return \Inertia\Inertia::render('HR/Leaves/Create', compact('employees'));
    }

    public function store(\App\Http\Requests\HR\StoreLeaveRequest $request)
    {
        $data = $request->validated();

        try {
            $leaveRequest = LeaveRequest::create($data);

            // Notify Administrators
            $admins = User::role('Administrator')->get();
            if ($admins->isEmpty()) {
                $admins = User::where('id', 1)->get();
            }

            try {
                Notification::send($admins, new LeaveRequestStatusNotification($leaveRequest, 'request'));
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new NewLeaveRequestMail($leaveRequest, auth()->user()));
                }
            } catch (\Exception $e) {
                \Log::error('Leave request notification dispatch failed: '.$e->getMessage());
            }

            return redirect()->route('hr.leaves.index')
                ->with('success', 'Leave request successfully filed and queued for administrative review.');

        } catch (\Exception $e) {
            \Log::error('Leave request recording failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Failed to process leave request. Please check system logs.');
        }
    }

    public function getLeaveDates(Employee $employee)
    {
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', [LeaveStatus::Pending->value, LeaveStatus::Approved->value])
            ->select('start_date', 'end_date', 'status')
            ->get()
            ->map(function ($l) {
                return [
                    'start' => $l->start_date->toDateString(),
                    'end' => $l->end_date->toDateString(),
                    'status' => $l->status,
                ];
            });

        return response()->json($leaves);
    }

    public function show(LeaveRequest $leave)
    {
        $leave->load('employee');

        return \Inertia\Inertia::render('HR/Leaves/Show', compact('leave'));
    }
}
