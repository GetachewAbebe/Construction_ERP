<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\GoodsReceivingNote;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProcurementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Inventory Manager', 'guard_name' => 'web']);
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create(['role' => 'Administrator']);
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_complete_procurement_cycle_from_requisition_to_goods_receipt(): void
    {
        $admin = $this->actingAdmin();

        $project = Project::create([
            'name' => 'Bole Commercial Complex',
            'status' => 'active',
            'budget' => 10000000.00,
        ]);

        $vendor = Vendor::create([
            'name' => 'National Cement Share Company',
            'code' => 'NAT-001',
            'is_active' => true,
            'payment_terms' => '30 Days Net',
        ]);

        $item = InventoryItem::create([
            'item_no' => 'MAT-CEM-01',
            'name' => 'Dangote OPC Cement 42.5R',
            'unit_of_measurement' => 'bags',
            'quantity' => 100,
            'store_location' => 'Central Yard',
            'in_date' => now()->toDateString(),
            'status' => 'in_stock',
        ]);

        // Step 1: Submit Purchase Requisition
        $prResponse = $this->actingAs($admin)->post(route('inventory.requisitions.store'), [
            'project_id' => $project->id,
            'required_date' => now()->addDays(5)->toDateString(),
            'priority' => 'high',
            'purpose' => 'Ground level footing casting',
            'remarks' => 'Batch testing certificate required',
            'items' => [
                [
                    'inventory_item_id' => $item->id,
                    'item_name' => 'Dangote OPC Cement 42.5R',
                    'unit_of_measurement' => 'bags',
                    'quantity_requested' => 200,
                    'estimated_unit_price' => 1250.00,
                    'specifications' => '42.5R Grade',
                ],
            ],
        ]);

        $prResponse->assertRedirect(route('inventory.requisitions.index'));
        $this->assertDatabaseHas('purchase_requisitions', [
            'purpose' => 'Ground level footing casting',
            'status' => 'pending',
        ]);

        $pr = PurchaseRequisition::where('purpose', 'Ground level footing casting')->firstOrFail();
        $this->assertCount(1, $pr->items);

        // Step 2: Approve Purchase Requisition
        $approveResponse = $this->actingAs($admin)->post(route('inventory.requisitions.approve', $pr->id), [
            'items' => [
                [
                    'id' => $pr->items->first()->id,
                    'quantity_approved' => 200,
                ],
            ],
        ]);

        $approveResponse->assertSessionHas('success');
        $pr->refresh();
        $this->assertEquals('approved', $pr->status);

        // Step 3: Issue Purchase Order from Requisition
        $poResponse = $this->actingAs($admin)->post(route('inventory.purchase-orders.store'), [
            'purchase_requisition_id' => $pr->id,
            'project_id' => $project->id,
            'vendor_id' => $vendor->id,
            'order_date' => now()->toDateString(),
            'delivery_due_date' => now()->addDays(4)->toDateString(),
            'delivery_site' => 'Bole Site Gate 2',
            'payment_terms' => '30 Days Net',
            'tax_rate' => 15,
            'notes' => 'Deliver on flatbed truck',
            'items' => [
                [
                    'inventory_item_id' => $item->id,
                    'item_name' => 'Dangote OPC Cement 42.5R',
                    'unit_of_measurement' => 'bags',
                    'quantity' => 200,
                    'unit_price' => 1200.00,
                ],
            ],
        ]);

        $po = PurchaseOrder::where('purchase_requisition_id', $pr->id)->firstOrFail();
        $poResponse->assertRedirect(route('inventory.purchase-orders.show', $po->id));

        $this->assertEquals(240000.00, (float) $po->subtotal);
        $this->assertEquals(36000.00, (float) $po->tax_amount);
        $this->assertEquals(276000.00, (float) $po->total_amount);

        $pr->refresh();
        $this->assertEquals('ordered', $pr->status);

        // Step 4: Record Delivery (Goods Receiving Note / GRN)
        $poItem = $po->items->first();
        $grnResponse = $this->actingAs($admin)->post(route('inventory.goods-receiving.store'), [
            'purchase_order_id' => $po->id,
            'received_date' => now()->toDateString(),
            'delivery_note_no' => 'WB-881923',
            'remarks' => 'All bags dry and intact',
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'inventory_item_id' => $item->id,
                    'item_name' => $poItem->item_name,
                    'unit_of_measurement' => $poItem->unit_of_measurement,
                    'quantity_delivered' => 200,
                    'quantity_accepted' => 200,
                    'quantity_rejected' => 0,
                    'rejection_reason' => null,
                ],
            ],
        ]);

        $grn = GoodsReceivingNote::where('purchase_order_id', $po->id)->firstOrFail();
        $grnResponse->assertRedirect(route('inventory.goods-receiving.show', $grn->id));

        // Step 5: Verify dynamic warehouse inventory increase
        $item->refresh();
        // 100 previous + 200 accepted = 300
        $this->assertEquals(300, $item->quantity);

        // Verify inventory audit log recorded
        $this->assertDatabaseHas('inventory_logs', [
            'inventory_item_id' => $item->id,
            'change_amount' => 200,
            'previous_quantity' => 100,
            'new_quantity' => 300,
            'reason' => 'grn_received',
        ]);

        // Step 6: Verify PO and PR status auto-completion
        $po->refresh();
        $this->assertEquals('received', $po->status);
        $this->assertEquals(200, (float) $po->items->first()->quantity_received);

        $pr->refresh();
        $this->assertEquals('completed', $pr->status);
    }
}
