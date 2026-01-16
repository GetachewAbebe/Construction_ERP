<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['FinancialManager', 'Administrator']);
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
