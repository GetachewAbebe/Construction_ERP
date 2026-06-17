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
}
