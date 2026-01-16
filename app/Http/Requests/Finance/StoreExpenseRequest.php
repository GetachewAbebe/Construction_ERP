<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'project_id'   => ['required', 'exists:projects,id'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'category'     => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'expense_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:255'],
        ];
    }
}
