<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['HumanResourceManager', 'Administrator']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $employee = $this->route('employee');
        
        return [
            'first_name'      => ['required', 'string', 'max:120'],
            'last_name'       => ['required', 'string', 'max:120'],
            'email'           => ['required', 'email', 'max:255', 'unique:employees,email,' . ($employee->id ?? $this->employee)],
            'phone'           => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'hire_date'       => ['nullable', 'date'],
            'salary'          => ['nullable', 'numeric'],
            'status'          => ['required', 'in:Active,On Leave,Terminated,Resigned'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'position_title'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
