<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\HR\StoreEmployeeRequest;
use App\Http\Requests\HR\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department_rel', 'position_rel']);

        // Search Scope
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('department_rel', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('position_rel', function($pq) use ($search) {
                      $pq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->get();
        $positions = \Illuminate\Support\Facades\DB::table('positions')->orderBy('title')->get();
        
        return view('hr.employees.create', compact('departments', 'positions'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        // Auto-Capitalize Names
        $data['first_name'] = ucwords(strtolower($data['first_name']));
        $data['last_name']  = ucwords(strtolower($data['last_name']));

        // Handle File Upload
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('employees', 'public');
        }

        // Process Department
        if (!empty($data['department_name'])) {
            $data['department_id'] = \App\Models\Department::firstOrCreate([
                'name' => ucwords(strtolower($data['department_name']))
            ])->id;
        }

        // Process Position
        if (!empty($data['position_title'])) {
            $data['position_id'] = \App\Models\Position::firstOrCreate([
                'title' => ucwords(strtolower($data['position_title']))
            ])->id;
        }

        Employee::create($data);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Professional identity successfully onboarded and archived.');
    }

    public function edit(Employee $employee)
    {
        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->get();
        $positions = \Illuminate\Support\Facades\DB::table('positions')->orderBy('title')->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        // Auto-Capitalize Names
        $data['first_name'] = ucwords(strtolower($data['first_name']));
        $data['last_name']  = ucwords(strtolower($data['last_name']));

        // Handle File Upload
        if ($request->hasFile('profile_picture')) {
            // Cleanup old picture
            if ($employee->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('employees', 'public');
        }

        // Process Department
        if (array_key_exists('department_name', $data)) {
            $data['department_id'] = !empty($data['department_name']) 
                ? \App\Models\Department::firstOrCreate(['name' => ucwords(strtolower($data['department_name']))])->id
                : null;
        }

        // Process Position
        if (array_key_exists('position_title', $data)) {
            $data['position_id'] = !empty($data['position_title'])
                ? \App\Models\Position::firstOrCreate(['title' => ucwords(strtolower($data['position_title']))])->id
                : null;
        }

        $employee->update($data);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Professional record updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee record archived.');
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
        return redirect()->route('hr.departments.index')->with('success', 'Department added to organizational structure.');
    }

    public function destroyDepartment($id)
    {
        \Illuminate\Support\Facades\DB::table('departments')->where('id', $id)->delete();
        return redirect()->route('hr.departments.index')->with('success', 'Department removed from structure.');
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
        return redirect()->route('hr.positions.index')->with('success', 'Position designation established.');
    }

    public function destroyPosition($id)
    {
        \Illuminate\Support\Facades\DB::table('positions')->where('id', $id)->delete();
        return redirect()->route('hr.positions.index')->with('success', 'Position designation retired.');
    }
}
