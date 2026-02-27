<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Administrator',
            'HumanResourceManager',
            'InventoryManager',
            'FinancialManager',
        ] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $email = 'administrator@natanemengineering.com';
        $pass = 'AdminNatanem@123';

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'middle_name' => '',
                'password' => Hash::make($pass),
                'role' => 'Administrator',
                'email_verified_at' => now()
            ]
        );

        // Keep password and role in sync with the provided one
        $admin->password = Hash::make($pass);
        $admin->role = 'Administrator';
        $admin->save();

        // Ensure role assignment
        $admin->syncRoles(['Administrator']);
    }
}
