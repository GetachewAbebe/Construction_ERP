<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Support both production (with spaces) and local (camelCase) role names
        return $this->user()->hasAnyRole([
            'FinancialManager',
            'Financial Manager',
            'Administrator',
            'Admin'
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location'    => ['nullable', 'string', 'max:255'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget'      => ['nullable', 'numeric', 'min:0'],
            'status'      => ['required', 'in:Planned,In Progress,Completed,On Hold,Cancelled'],
        ];
    }
}
