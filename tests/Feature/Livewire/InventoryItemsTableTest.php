<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Inventory\ItemsTable;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryItemsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders(): void
    {
        Livewire::test(ItemsTable::class)
            ->assertOk()
            ->assertSee('Total items')
            ->assertSee('No items match your filters.');
    }

    public function test_create_item_persists_and_logs_initial_entry(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ItemsTable::class)
            ->call('create')
            ->set('item_no', 'POC-100')
            ->set('name', 'Test Generator')
            ->set('unit_of_measurement', 'pcs')
            ->set('quantity', 8)
            ->set('store_location', 'Yard A')
            ->set('in_date', now()->toDateString())
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false);

        $item = InventoryItem::where('item_no', 'POC-100')->first();
        $this->assertNotNull($item);
        $this->assertSame(8, (int) $item->quantity);
        $this->assertSame(1, InventoryLog::where('inventory_item_id', $item->id)->where('reason', 'Initial Entry')->count());
    }

    public function test_editing_quantity_routes_through_adjustment_log(): void
    {
        $user = User::factory()->create();
        $item = InventoryItem::create([
            'item_no' => 'POC-200', 'name' => 'Welder', 'unit_of_measurement' => 'pcs',
            'quantity' => 5, 'store_location' => 'Yard B', 'in_date' => now()->toDateString(), 'status' => 'available',
        ]);

        Livewire::actingAs($user)
            ->test(ItemsTable::class)
            ->call('editItem', $item->id)
            ->set('quantity', 12)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(12, (int) $item->fresh()->quantity);
        $this->assertSame(1, InventoryLog::where('inventory_item_id', $item->id)->where('reason', 'Manual Adjustment')->count());
    }
}
