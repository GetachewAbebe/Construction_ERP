<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Support both production (with spaces) and local (camelCase) role names
        return $this->user()->hasAnyRole([
            'InventoryManager',
            'Inventory Manager',
            'Administrator',
            'Admin',
        ]);
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'requested_at' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
