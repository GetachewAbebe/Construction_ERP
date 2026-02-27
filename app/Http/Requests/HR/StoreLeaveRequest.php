<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Any authenticated user can potentially submit a leave request (self)
        // HR/Admins can also submit on behalf of others
        // Support both production (with spaces) and local (camelCase) role names
        return $this->user()->hasAnyRole([
            'HumanResourceManager',
            'Human Resource Manager',
            'Administrator',
            'Admin',
            'Employee',
        ]) || $this->user()->employee_id > 0;
        // Adjust based on your specific role hierarchy. For now, allow all authenticated users.
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $employeeId = $this->input('employee_id');
                    $endDate = $this->input('end_date');

                    if (! $employeeId || ! $endDate) {
                        return;
                    }

                    $overlap = \App\Models\LeaveRequest::where('employee_id', $employeeId)
                        ->whereIn('status', ['Pending', 'Approved']) // Only check active requests
                        ->where(function ($query) use ($value, $endDate) {
                            $query->whereBetween('start_date', [$value, $endDate])
                                ->orWhereBetween('end_date', [$value, $endDate])
                                ->orWhere(function ($q) use ($value, $endDate) {
                                    $q->where('start_date', '<', $value)
                                        ->where('end_date', '>', $endDate);
                                });
                        })
                        ->exists();

                    if ($overlap) {
                        $fail('The selected employee already has a pending or approved leave request for this period.');
                    }
                },
            ],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
