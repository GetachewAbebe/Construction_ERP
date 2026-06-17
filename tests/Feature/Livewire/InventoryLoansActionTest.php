<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Inventory\LoansTable;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryLoansActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_approve_action_updates_status_and_deducts_stock(): void
    {
        $user = User::factory()->create();
        $item = InventoryItem::create([
            'item_no' => 'TST-001', 'name' => 'Test Drill', 'quantity' => 10, 'status' => 'available',
        ]);
        $emp = Employee::create([
            'first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane.doe@example.test', 'status' => 'active',
        ]);
        $loan = InventoryLoan::create([
            'inventory_item_id' => $item->id, 'employee_id' => $emp->id,
            'requested_by_user_id' => $user->id, 'quantity' => 3, 'status' => 'pending',
            'requested_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(LoansTable::class)
            ->call('approve', $loan->id)
            ->assertHasNoErrors();

        $this->assertSame('approved', $loan->fresh()->status);
        $this->assertSame(7, $item->fresh()->quantity); // 10 - 3
    }

    public function test_approving_an_already_processed_loan_does_not_deduct_stock_again(): void
    {
        $user = User::factory()->create();
        $item = InventoryItem::create([
            'item_no' => 'TST-002', 'name' => 'Test Saw', 'quantity' => 10, 'status' => 'available',
        ]);
        $emp = Employee::create([
            'first_name' => 'John', 'last_name' => 'Roe', 'email' => 'john.roe@example.test', 'status' => 'active',
        ]);
        // Loan is already approved (simulates a second approval racing in after the first committed).
        $loan = InventoryLoan::create([
            'inventory_item_id' => $item->id, 'employee_id' => $emp->id,
            'requested_by_user_id' => $user->id, 'quantity' => 3, 'status' => 'approved',
            'requested_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(LoansTable::class)
            ->call('approve', $loan->id)
            ->assertHasNoErrors();

        // Status unchanged and stock untouched — the re-check inside the lock rejected it.
        $this->assertSame('approved', $loan->fresh()->status);
        $this->assertSame(10, $item->fresh()->quantity);
    }
}
