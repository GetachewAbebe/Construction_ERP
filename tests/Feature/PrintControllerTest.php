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

    public function test_can_render_printable_purchase_order_and_qr_verify(): void
    {
        $user = User::factory()->create();
        $vendor = \App\Models\Vendor::create([
            'name' => 'National Steel Rolling Mill',
            'code' => 'NSR-001',
            'is_active' => true,
        ]);
        $project = Project::create([
            'name' => 'Bole Commercial Complex',
            'budget' => 50000000,
        ]);

        $po = \App\Models\PurchaseOrder::create([
            'po_no' => 'PO-2026-0001',
            'vendor_id' => $vendor->id,
            'project_id' => $project->id,
            'created_by' => $user->id,
            'order_date' => now()->toDateString(),
            'status' => 'issued',
            'subtotal' => 100000,
            'tax_rate' => 15,
            'tax_amount' => 15000,
            'total_amount' => 115000,
        ]);

        \App\Models\PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'item_name' => 'Reinforcement Steel Bar Ø20mm',
            'unit_of_measurement' => 'tons',
            'quantity' => 10,
            'unit_price' => 10000,
            'total_price' => 100000,
        ]);

        $response = $this->actingAs($user)->get(route('prints.purchase-order', $po));
        $response->assertOk()
            ->assertSee('OFFICIAL PURCHASE ORDER')
            ->assertSee('National Steel Rolling Mill')
            ->assertSee('Reinforcement Steel Bar Ø20mm')
            ->assertSee('SCAN TO VERIFY');

        // Test public/field QR verification endpoint
        $verifyResponse = $this->get(route('verify.purchase-order', $po));
        $verifyResponse->assertOk()
            ->assertSee('PO-2026-0001')
            ->assertSee('National Steel Rolling Mill');
    }

    public function test_can_render_printable_goods_receiving_note_and_qr_verify(): void
    {
        $user = User::factory()->create();
        $vendor = \App\Models\Vendor::create([
            'name' => 'Muger Cement Enterprise',
            'code' => 'MUG-001',
            'is_active' => true,
        ]);
        $project = Project::create([
            'name' => 'Kazanchis Site',
            'budget' => 50000000,
        ]);

        $po = \App\Models\PurchaseOrder::create([
            'po_no' => 'PO-2026-0002',
            'vendor_id' => $vendor->id,
            'project_id' => $project->id,
            'created_by' => $user->id,
            'order_date' => now()->toDateString(),
            'status' => 'issued',
            'total_amount' => 50000,
        ]);

        $grn = \App\Models\GoodsReceivingNote::create([
            'grn_no' => 'GRN-2026-0001',
            'purchase_order_id' => $po->id,
            'project_id' => $project->id,
            'vendor_id' => $vendor->id,
            'received_by' => $user->id,
            'received_date' => now()->toDateString(),
            'delivery_note_no' => 'DN-99412',
            'status' => 'received',
        ]);

        \App\Models\GoodsReceivingNoteItem::create([
            'goods_receiving_note_id' => $grn->id,
            'item_name' => 'Ordinary Portland Cement',
            'unit_of_measurement' => 'bags',
            'quantity_delivered' => 100,
            'quantity_accepted' => 95,
            'quantity_rejected' => 5,
            'rejection_reason' => 'Torn bags',
        ]);

        $response = $this->actingAs($user)->get(route('prints.goods-receiving', $grn));
        $response->assertOk()
            ->assertSee('Store Receiving Voucher')
            ->assertSee('GRN-2026-0001')
            ->assertSee('Ordinary Portland Cement')
            ->assertSee('DN-99412');

        // Test public QR verification endpoint
        $verifyResponse = $this->get(route('verify.goods-receiving', $grn));
        $verifyResponse->assertOk()
            ->assertSee('GRN-2026-0001')
            ->assertSee('Muger Cement Enterprise');
    }
}
