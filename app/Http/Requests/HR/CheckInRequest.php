<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Any authenticated user with an employee profile can check in
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'exists:employees,id'],
        ];
    }
}
