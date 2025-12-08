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
            'first_name'      => ['required','string','max:120'],
            'last_name'       => ['required','string','max:120'],
            'email'           => ['required','email','max:255','unique:employees,email'],
            'phone'           => ['nullable','string','max:20'],
            'hire_date'       => ['nullable','date'],
            'salary'          => ['nullable','numeric'],
            'department_name' => ['nullable','string','max:255'],
            'position_title'  => ['nullable','string','max:255'],
        ]);

        // Process Department
        $departmentId = null;
        if (!empty($data['department_name'])) {
            $existing = \Illuminate\Support\Facades\DB::table('departments')
                ->where('name', $data['department_name'])
                ->first();
            
            if ($existing) {
                $departmentId = $existing->id;
            } else {
                $departmentId = \Illuminate\Support\Facades\DB::table('departments')->insertGetId([
                    'name' => $data['department_name'],
                    'created_at' => now(), 
                    'updated_at' => now()
                ]);
            }
        }
        $data['department_id'] = $departmentId;
        unset($data['department_name']);

        // Process Position
        $positionId = null;
        if (!empty($data['position_title'])) {
            $existingPos = \Illuminate\Support\Facades\DB::table('positions')
                ->where('title', $data['position_title'])
                ->first();
            
            if ($existingPos) {
                $positionId = $existingPos->id;
            } else {
                $positionId = \Illuminate\Support\Facades\DB::table('positions')->insertGetId([
                    'title' => $data['position_title'],
                    'created_at' => now(), 
                    'updated_at' => now()
                ]);
            }
        }
        $data['position_id'] = $positionId;
        unset($data['position_title']);

        // Clean up any other temporary fields if necessary (already unset above)
        // Note: 'new_department' and 'new_position' are gone from validation

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
            'first_name'      => ['required','string','max:120'],
            'last_name'       => ['required','string','max:120'],
            'email'           => ['required','email','max:255','unique:employees,email,'.$employee->id],
            'phone'           => ['nullable','string','max:20'],
            'hire_date'       => ['nullable','date'],
            'salary'          => ['nullable','numeric'],
            'department_name' => ['nullable','string','max:255'],
            'position_title'  => ['nullable','string','max:255'],
        ]);

        // Process Department
        $departmentId = $employee->department_id; 
        
        // Check if department_name is present in the request
        if (array_key_exists('department_name', $data)) {
            if (empty($data['department_name'])) {
                $departmentId = null; 
            } else {
                $existing = \Illuminate\Support\Facades\DB::table('departments')
                    ->where('name', $data['department_name'])
                    ->first();

                if ($existing) {
                    $departmentId = $existing->id;
                } else {
                    $departmentId = \Illuminate\Support\Facades\DB::table('departments')->insertGetId([
                        'name' => $data['department_name'],
                        'created_at' => now(), 
                        'updated_at' => now()
                    ]);
                }
            }
            unset($data['department_name']);
        }
        $data['department_id'] = $departmentId;

        // Process Position
        $positionId = $employee->position_id;
        
        // Check if position_title is present in the request
        if (array_key_exists('position_title', $data)) {
            if (empty($data['position_title'])) {
                $positionId = null;
            } else {
                $existingPos = \Illuminate\Support\Facades\DB::table('positions')
                    ->where('title', $data['position_title'])
                    ->first();

                if ($existingPos) {
                    $positionId = $existingPos->id;
                } else {
                    $positionId = \Illuminate\Support\Facades\DB::table('positions')->insertGetId([
                        'title' => $data['position_title'],
                        'created_at' => now(), 
                        'updated_at' => now()
                    ]);
                }
            }
            unset($data['position_title']);
        }
        $data['position_id'] = $positionId;

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

    // ==========================================
    // DEPARTMENT MANAGEMENT
    // ==========================================
    public function indexDepartments()
    {
        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->paginate(20);
        return view('hr.departments.create', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255|unique:departments,name']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        \Illuminate\Support\Facades\DB::table('departments')->insert($data);
        return redirect()->route('hr.departments.index')->with('status', 'Department added.');
    }

    public function destroyDepartment($id)
    {
        \Illuminate\Support\Facades\DB::table('departments')->where('id', $id)->delete();
        return redirect()->route('hr.departments.index')->with('status', 'Department deleted.');
    }

    // ==========================================
    // POSITION MANAGEMENT
    // ==========================================
    public function indexPositions()
    {
        $positions = \Illuminate\Support\Facades\DB::table('positions')->orderBy('title')->paginate(20);
        return view('hr.positions.create', compact('positions'));
    }

    public function storePosition(Request $request)
    {
        $data = $request->validate(['title' => 'required|string|max:255|unique:positions,title']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        \Illuminate\Support\Facades\DB::table('positions')->insert($data);
        return redirect()->route('hr.positions.index')->with('status', 'Position added.');
    }

    public function destroyPosition($id)
    {
        \Illuminate\Support\Facades\DB::table('positions')->where('id', $id)->delete();
        return redirect()->route('hr.positions.index')->with('status', 'Position deleted.');
    }
}
