<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->paginate(20);
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->get();
        $positions = \Illuminate\Support\Facades\DB::table('positions')->orderBy('title')->get();
        
        return view('hr.employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => ['required','string','max:120'],
            'last_name'     => ['required','string','max:120'],
            'email'         => ['required','email','max:255','unique:employees,email'],
            'hire_date'     => ['nullable','date'],
            'salary'        => ['nullable','numeric'],
            'department_id' => ['nullable','exists:departments,id'],
            'position_id'   => ['nullable','exists:positions,id'],
        ]);

        Employee::create($data);

        return redirect()->route('hr.employees.index')
            ->with('status','Employee created.');
    }

    public function edit(Employee $employee)
    {
        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->get();
        $positions = \Illuminate\Support\Facades\DB::table('positions')->orderBy('title')->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'first_name'    => ['required','string','max:120'],
            'last_name'     => ['required','string','max:120'],
            'email'         => ['required','email','max:255','unique:employees,email,'.$employee->id],
            'hire_date'     => ['nullable','date'],
            'salary'        => ['nullable','numeric'],
            'department_id' => ['nullable','exists:departments,id'],
            'position_id'   => ['nullable','exists:positions,id'],
        ]);

        $employee->update($data);

        return redirect()->route('hr.employees.index')
            ->with('status','Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')
            ->with('status','Employee deleted.');
    }
}
