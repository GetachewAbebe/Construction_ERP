<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Expense;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Departments and Positions exist
        $depts = ['Engineering', 'Operations', 'Logistics', 'Finance', 'Human Resources'];
        foreach ($depts as $dept) {
            DB::table('departments')->updateOrInsert(['name' => $dept], ['created_at' => now(), 'updated_at' => now()]);
        }

        $positions = ['Administrator', 'Human Resource Manager', 'Inventory Manager', 'Financial Manager', 'Engineer', 'Site Supervisor', 'Accountant', 'Staff'];
        foreach ($positions as $pos) {
            DB::table('positions')->updateOrInsert(['title' => $pos], ['created_at' => now(), 'updated_at' => now()]);
        }

        $deptMap = DB::table('departments')->pluck('id', 'name')->toArray();
        $posMap = DB::table('positions')->pluck('id', 'title')->toArray();

        // 2. Create specific Managers
        $managers = [
            [
                'first_name' => 'HR',
                'last_name' => 'Manager',
                'email' => 'humanresource@natanemengineering.com',
                'password' => 'HumanResource@123',
                'role' => 'HumanResourceManager',
                'dept' => 'Human Resources',
                'pos' => 'Human Resource Manager'
            ],
            [
                'first_name' => 'Inventory',
                'last_name' => 'Manager',
                'email' => 'inventorymanager@natanemengineering.com',
                'password' => 'InventoryManager@123',
                'role' => 'InventoryManager',
                'dept' => 'Logistics',
                'pos' => 'Inventory Manager'
            ],
            [
                'first_name' => 'Finance',
                'last_name' => 'Manager',
                'email' => 'financialmanager@natanemengineering.com',
                'password' => 'FinancialManager@123',
                'role' => 'FinancialManager',
                'dept' => 'Finance',
                'pos' => 'Financial Manager'
            ],
        ];

        foreach ($managers as $mgrData) {
            $user = User::updateOrCreate(
                ['email' => $mgrData['email']],
                [
                    'first_name' => $mgrData['first_name'],
                    'last_name' => $mgrData['last_name'],
                    'middle_name' => '',
                    'password' => Hash::make($mgrData['password']),
                    'role' => $mgrData['role'],
                    'email_verified_at' => now(),
                ]
            );
            // Ensure password and role are in sync even if user exists
            $user->password = Hash::make($mgrData['password']);
            $user->role = $mgrData['role'];
            $user->save();
            $user->syncRoles([$mgrData['role']]);

            // Create/Update linked employee record
            Employee::updateOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'department_id' => $deptMap[$mgrData['dept']] ?? null,
                    'position_id' => $posMap[$mgrData['pos']] ?? null,
                    'status' => 'Active',
                    'hire_date' => now()->subYear(),
                ]
            );
        }

        // 3. Create sample Projects
        $projects = [
            ['name' => 'Bole Road Rejuvenation', 'description' => 'Upgrading of Bole road intersection.', 'location' => 'Addis Ababa', 'budget' => 50000000.00, 'status' => 'active'],
            ['name' => 'Derba Site Expansion', 'description' => 'Construction of new storage facilities.', 'location' => 'Derba', 'budget' => 15000000.00, 'status' => 'active'],
            ['name' => 'Waserbi Warehouse', 'description' => 'Steel structure assembly.', 'location' => 'Waserbi', 'budget' => 25000000.00, 'status' => 'active'],
        ];

        foreach ($projects as $projData) {
            Project::firstOrCreate(['name' => $projData['name']], $projData);
        }

        // 4. Create Inventory Items
        $items = [
            ['item_no' => 'EQ-001', 'name' => 'Concrete Mixer 750L', 'quantity' => 5, 'unit_of_measurement' => 'pcs', 'store_location' => 'Waserbi'],
            ['item_no' => 'EQ-002', 'name' => 'Excavator CAT 320', 'quantity' => 2, 'unit_of_measurement' => 'pcs', 'store_location' => 'Derba'],
            ['item_no' => 'MT-001', 'name' => 'Reinforcement Bar 12mm', 'quantity' => 500, 'unit_of_measurement' => 'kg', 'store_location' => 'Addis Ababa'],
            ['item_no' => 'MT-002', 'name' => 'Cement Grade 42.5', 'quantity' => 1000, 'unit_of_measurement' => 'bags', 'store_location' => 'Derba'],
        ];

        foreach ($items as $itemData) {
            InventoryItem::firstOrCreate(['item_no' => $itemData['item_no']], $itemData);
        }

        // 5. Create random Employees
        User::factory(10)->create()->each(function ($user) use ($deptMap, $posMap) {
            Employee::create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'department_id' => collect($deptMap)->random(),
                'position_id' => $posMap['Staff'],
                'status' => 'Active',
                'hire_date' => now()->subMonths(rand(1, 24)),
            ]);
        });

        // 6. Create Expenses
        $projectIds = Project::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            Expense::create([
                'project_id' => collect($projectIds)->random(),
                'user_id' => collect($userIds)->random(),
                'amount' => rand(1000, 50000),
                'category' => collect(['materials', 'labor', 'transport', 'equipment', 'utility', 'other'])->random(),
                'description' => 'Sample expense.',
                'expense_date' => now()->subDays(rand(1, 30)),
                'reference_no' => 'INV-' . rand(10000, 99999),
            ]);
        }
    }
}
