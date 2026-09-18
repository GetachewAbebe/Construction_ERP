<?php

declare(strict_types=1);

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeRequest;
use App\Http\Requests\HR\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Services\HumanResourceService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly HumanResourceService $hrService,
    ) {}

    public function index(Request $request)
    {
        $employees = $this->hrService->getEmployees($request->only(['q', 'status']));

        return \Inertia\Inertia::render('HR/Employees/Index', [
            'employees' => $employees,
            'q' => $request->q ?? '',
            'status' => $request->status ?? '',
        ]);
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return \Inertia\Inertia::render('HR/Employees/Create', compact('departments', 'positions'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->hrService->storeEmployee($request->validated() + ['profile_picture' => $request->file('profile_picture')]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Professional identity successfully onboarded and archived.');
    }

    public function edit(Employee $employee)
    {
        $employee->load(['department_rel', 'position_rel']);
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return \Inertia\Inertia::render('HR/Employees/Edit', compact('employee', 'departments', 'positions'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->hrService->updateEmployee($employee, $request->validated() + ['profile_picture' => $request->file('profile_picture')]);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Professional record updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->hrService->deleteEmployee($employee);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee record archived.');
    }

    // ==========================================
    // DEPARTMENT MANAGEMENT
    // ==========================================

    public function indexDepartments()
    {
        $departments = Department::orderBy('name')->paginate(20);

        return view('hr.departments.create', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:departments,name']);

        $this->hrService->getOrCreateDepartment($request->name);

        return redirect()->route('hr.departments.index')->with('success', 'Department added to organizational structure.');
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();

        return redirect()->route('hr.departments.index')->with('success', 'Department removed from structure.');
    }

    // ==========================================
    // POSITION MANAGEMENT
    // ==========================================

    public function indexPositions()
    {
        $positions = Position::orderBy('title')->paginate(20);

        return view('hr.positions.create', compact('positions'));
    }

    public function storePosition(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255|unique:positions,title']);

        $this->hrService->getOrCreatePosition($request->title);

        return redirect()->route('hr.positions.index')->with('success', 'Position designation established.');
    }

    public function destroyPosition(Position $position)
    {
        $position->delete();

        return redirect()->route('hr.positions.index')->with('success', 'Position designation retired.');
    }
}
