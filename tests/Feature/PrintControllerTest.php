<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_printable_expense_payment_voucher(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Kazanchis Headquarters Tower',
            'budget' => 95000000,
        ]);

        $expense = Expense::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'amount' => 45000.00,
            'category' => 'Materials',
            'description' => 'Procurement of structural steel brackets',
            'expense_date' => now()->toDateString(),
            'status' => 'approved',
            'approved_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('prints.expense-voucher', $expense));

        $response->assertOk()
            ->assertSee('PAYMENT VOUCHER')
            ->assertSee('Kazanchis Headquarters Tower')
            ->assertSee('45,000');

        $directResponse = $this->actingAs($user)->get("/prints/expense-voucher/{$expense->id}");
        $directResponse->assertOk()
            ->assertSee('PAYMENT VOUCHER');
    }

    public function test_can_render_printable_inventory_loan_gate_pass(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'first_name' => 'Abebe',
            'last_name' => 'Bekele',
            'email' => 'abebe.bekele@natanem.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $item = InventoryItem::create([
            'item_no' => 'EQP-GEN-01',
            'name' => 'Bosch Rotary Hammer Drill',
            'unit_of_measurement' => 'pcs',
            'quantity' => 5,
            'store_location' => 'Zone B Shelf 4',
            'in_date' => now()->toDateString(),
        ]);

        $loan = InventoryLoan::create([
            'inventory_item_id' => $item->id,
            'employee_id' => $employee->id,
            'requested_by_user_id' => $user->id,
            'quantity' => 1,
            'status' => 'approved',
            'requested_at' => now(),
            'due_date' => now()->addDays(7)->toDateString(),
            'remarks' => 'Concrete anchor drilling at 4th floor slab',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('prints.loan-gate-pass', $loan));

        $response->assertOk()
            ->assertSee('MATERIAL GATE PASS')
            ->assertSee('Bosch Rotary Hammer Drill')
            ->assertSee('Abebe Bekele');

        // Test direct URL paths
        $directResponse = $this->actingAs($user)->get("/prints/loan-gate-pass/{$loan->id}");
        $directResponse->assertOk()
            ->assertSee('MATERIAL GATE PASS');
    }
}
