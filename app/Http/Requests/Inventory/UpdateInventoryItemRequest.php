<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Support both production (with spaces) and local (camelCase) role names
        return $this->user()->hasAnyRole([
            'InventoryManager',
            'Inventory Manager',
            'Administrator',
            'Admin'
        ]);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('quantity')) {
            $this->merge([
                'quantity' => (int) preg_replace('/[^\d\-]/', '', (string) $this->input('quantity'))
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'item_no'             => ['required', 'string', 'max:255'],
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string', 'max:1000'],
            'unit_of_measurement' => ['required', 'string', 'max:255'],
            'quantity'            => ['required', 'integer', 'min:0'],
            'store_location'      => ['required', 'string', 'max:255'],
            'in_date'             => ['required', 'date'],
        ];
    }
}
