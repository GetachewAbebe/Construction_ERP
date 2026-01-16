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
        $pass  = 'AdminNatanem@123';

        $admin = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'System Admin', 'password' => Hash::make($pass), 'email_verified_at' => now()]
        );

        // Keep password in sync with the provided one
        $admin->password = Hash::make($pass);
        $admin->save();

        // Ensure role assignment
        $admin->syncRoles(['Administrator']);
    }
}
