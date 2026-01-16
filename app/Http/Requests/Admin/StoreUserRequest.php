<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrator');
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', Password::min(8)],
            'role'            => ['required', Rule::in(['Administrator', 'HumanResourceManager', 'InventoryManager', 'FinancialManager'])],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'phone_number'    => ['nullable', 'string', 'max:20'],
            'position'        => ['nullable', 'string', 'max:255'],
            'department'      => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'in:Active,Inactive,Suspended'],
        ];
    }
}
