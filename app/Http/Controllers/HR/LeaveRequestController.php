<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::with('employee')->latest()->paginate(20);
        return view('hr.leaves.index', compact('requests'));
    }

    public function create()
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id','first_name','last_name']);
        return view('hr.leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required','exists:employees,id'],
            'start_date'  => ['required','date'],
            'end_date'    => ['required','date','after_or_equal:start_date'],
            'reason'      => ['nullable','string','max:500'],
        ]);

        LeaveRequest::create($data);

        return redirect()->route('hr.leaves.index')
            ->with('status','Leave request submitted and awaiting Admin approval.');
    }
}
