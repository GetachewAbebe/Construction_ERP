<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HumanResourceService
{
    /**
     * Get employees with filtering and pagination.
     */
    public function getEmployees(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Employee::with(['department_rel', 'position_rel']);

        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('department_rel', function ($dq) use ($search) {
                        $dq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('position_rel', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Store a new employee.
     */
    public function storeEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $data = $this->processEmployeeData($data);

            // Handle Profile Picture
            if (! empty($data['profile_picture']) && is_object($data['profile_picture'])) {
                $data['profile_picture'] = $data['profile_picture']->store('employees', 'public');
            }

            // Sync Department & Position
            if (! empty($data['department_name'])) {
                $data['department_id'] = $this->getOrCreateDepartment($data['department_name'])->id;
            }

            if (! empty($data['position_title'])) {
                $data['position_id'] = $this->getOrCreatePosition($data['position_title'])->id;
            }

            return Employee::create($data);
        });
    }

    /**
     * Update an existing employee.
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $data = $this->processEmployeeData($data);

            // Handle Profile Picture
            if (! empty($data['profile_picture']) && is_object($data['profile_picture'])) {
                if ($employee->profile_picture) {
                    Storage::disk('public')->delete($employee->profile_picture);
                }
                $data['profile_picture'] = $data['profile_picture']->store('employees', 'public');
            }

            // Sync Department & Position
            if (array_key_exists('department_name', $data)) {
                $data['department_id'] = ! empty($data['department_name'])
                    ? $this->getOrCreateDepartment($data['department_name'])->id
                    : null;
            }

            if (array_key_exists('position_title', $data)) {
                $data['position_id'] = ! empty($data['position_title'])
                    ? $this->getOrCreatePosition($data['position_title'])->id
                    : null;
            }

            $employee->update($data);

            return $employee;
        });
    }

    /**
     * Archive an employee.
     */
    public function deleteEmployee(Employee $employee): bool
    {
        return $employee->delete();
    }

    /**
     * Standardize employee data (Capitalization, etc.)
     */
    protected function processEmployeeData(array $data): array
    {
        if (! empty($data['first_name'])) {
            $data['first_name'] = ucwords(strtolower($data['first_name']));
        }
        if (! empty($data['last_name'])) {
            $data['last_name'] = ucwords(strtolower($data['last_name']));
        }

        return $data;
    }

    /**
     * Department helper.
     */
    public function getOrCreateDepartment(string $name): Department
    {
        return Department::firstOrCreate([
            'name' => ucwords(strtolower($name)),
        ]);
    }

    /**
     * Position helper.
     */
    public function getOrCreatePosition(string $title): Position
    {
        return Position::firstOrCreate([
            'title' => ucwords(strtolower($title)),
        ]);
    }
}
