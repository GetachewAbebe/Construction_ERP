<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\MaintenanceWorkOrder;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FleetMaintenanceWorkflowTest extends TestCase
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

    public function test_complete_fleet_maintenance_work_order_lifecycle(): void
    {
        $admin = $this->actingAdmin();

        $project = Project::create([
            'name' => 'Addis Ring Road Interchange',
            'location' => 'Addis Ababa',
            'budget' => 60000000.00,
            'status' => 'in_progress',
        ]);

        // 1. Setup Equipment Fleet Machine
        $machine = Equipment::create([
            'name' => 'CAT 330D Hydraulic Excavator',
            'plate_number' => 'ET-03-A7821',
            'type' => 'Excavator',
            'project_id' => $project->id,
            'status' => 'operational',
            'operating_hours' => 248.00,
            'next_service_hours' => 250.00,
            'fuel_type' => 'Diesel',
        ]);

        $this->assertEquals('due_soon', $machine->service_status);

        // 2. Setup Warehouse Spare Parts & Fluids
        $oilItem = InventoryItem::create([
            'item_no' => 'OIL-15W40-01',
            'name' => 'Engine Oil 15W-40 (Bulk Drum)',
            'quantity' => 100,
            'unit_of_measurement' => 'liters',
            'status' => 'in_stock',
        ]);

        $filterItem = InventoryItem::create([
            'item_no' => 'FLT-CAT-0716',
            'name' => 'CAT Primary Fuel Filter',
            'quantity' => 20,
            'unit_of_measurement' => 'pcs',
            'status' => 'in_stock',
        ]);

        // 3. Open Maintenance Work Order
        $this->actingAs($admin);

        $workOrderData = [
            'equipment_id' => $machine->id,
            'project_id' => $project->id,
            'order_type' => 'preventive',
            'priority' => 'routine',
            'title' => '250-Hour Periodic Engine & Filters Service',
            'operating_hours' => 248.00,
            'fault_description' => 'Scheduled periodic maintenance due at 250 hours.',
            'assigned_mechanic_id' => $admin->id,
            'start_date' => '2026-09-26',
            'labor_cost' => 1500.00,
            'external_cost' => 0.00,
            'parts' => [
                [
                    'inventory_item_id' => $oilItem->id,
                    'part_name' => 'Engine Oil 15W-40 (Bulk Drum)',
                    'part_number' => 'OIL-15W40',
                    'quantity' => 20.00,
                    'unit_of_measurement' => 'liters',
                    'unit_price' => 250.00,
                ],
                [
                    'inventory_item_id' => $filterItem->id,
                    'part_name' => 'CAT Primary Fuel Filter',
                    'part_number' => '1R-0716',
                    'quantity' => 1.00,
                    'unit_of_measurement' => 'pcs',
                    'unit_price' => 1200.00,
                ],
            ],
        ];

        $storeResponse = $this->post(route('operations.maintenance.store'), $workOrderData);
        $workOrder = MaintenanceWorkOrder::orderByDesc('id')->first();
        $this->assertNotNull($workOrder);
        $storeResponse->assertRedirect(route('operations.maintenance.show', $workOrder->id));

        // Math Verification:
        // Oil: 20 * 250 = 5,000 ETB
        // Filter: 1 * 1200 = 1,200 ETB
        // Parts total: 6,200 ETB
        // Labor: 1,500 ETB
        // Grand total: 7,700 ETB
        $this->assertEquals(6200.00, (float) $workOrder->parts_cost);
        $this->assertEquals(1500.00, (float) $workOrder->labor_cost);
        $this->assertEquals(7700.00, (float) $workOrder->total_cost);
        $this->assertEquals('in_progress', $workOrder->status);

        // Machine status moved to maintenance
        $machine->refresh();
        $this->assertEquals('maintenance', $machine->status);

        // 4. Complete and Certify Maintenance Work Order
        $completeResponse = $this->post(route('operations.maintenance.complete', $workOrder->id), [
            'final_operating_hours' => 255.00,
            'downtime_hours' => 4.50,
            'work_performed' => 'Engine oil drained and filled with 20L 15W-40. Fuel filter 1R-0716 replaced. Pressure tested OK.',
        ]);

        $completeResponse->assertRedirect();
        $workOrder->refresh();
        $this->assertEquals('completed', $workOrder->status);
        $this->assertEquals(4.50, (float) $workOrder->downtime_hours);
        $this->assertNotNull($workOrder->completed_date);
        $this->assertEquals($admin->id, $workOrder->completed_by);

        // Verify machine restored to operational status and next service advanced
        $machine->refresh();
        $this->assertEquals('operational', $machine->status);
        $this->assertEquals(255.00, (float) $machine->operating_hours);
        $this->assertEquals(505.00, (float) $machine->next_service_hours); // 255 + 250 = 505
        $this->assertEquals('ok', $machine->service_status);

        // Verify automated warehouse inventory stock deduction
        $oilItem->refresh();
        $filterItem->refresh();
        $this->assertEquals(80, $oilItem->quantity); // 100 - 20 = 80
        $this->assertEquals(19, $filterItem->quantity); // 20 - 1 = 19

        // Verify inventory log audit trail
        $this->assertDatabaseHas('inventory_logs', [
            'inventory_item_id' => $oilItem->id,
            'change_amount' => -20,
            'new_quantity' => 80,
        ]);
        $this->assertDatabaseHas('inventory_logs', [
            'inventory_item_id' => $filterItem->id,
            'change_amount' => -1,
            'new_quantity' => 19,
        ]);

        // Verify project expense auto-posting
        $this->assertNotNull($workOrder->expense_id);
        $expense = Expense::find($workOrder->expense_id);
        $this->assertNotNull($expense);
        $this->assertEquals($project->id, $expense->project_id);
        $this->assertEquals(7700.00, (float) $expense->amount);
        $this->assertEquals('Equipment', $expense->category);
        $this->assertEquals($workOrder->work_order_no, $expense->reference_no);

        // Verify equipment total maintenance cost calculation
        $this->assertEquals(7700.00, (float) $machine->total_maintenance_cost);

        // 5. Test Field QR Mobile Verification
        $verifyResponse = $this->get(route('verify.maintenance-order', $workOrder->work_order_no));
        $verifyResponse->assertOk();
        $verifyResponse->assertSee($workOrder->work_order_no);
        $verifyResponse->assertSee('CAT 330D Hydraulic Excavator');
        $verifyResponse->assertSee('ET-03-A7821');
        $verifyResponse->assertSee('7,700.00 ETB');
        $verifyResponse->assertSee('Engine Oil 15W-40');

        // 6. Test Corporate Printable Job Card
        $printResponse = $this->get(route('operations.maintenance.print', $workOrder->id));
        $printResponse->assertOk();
        $printResponse->assertSee('MAINTENANCE WORK ORDER');
        $printResponse->assertSee('CAT 330D Hydraulic Excavator');
        $printResponse->assertSee('7,700.00 ETB');
        $printResponse->assertSee('SCAN TO VERIFY');
    }
}
