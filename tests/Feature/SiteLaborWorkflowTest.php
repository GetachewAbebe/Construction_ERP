<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CasualLaborer;
use App\Models\Expense;
use App\Models\MusterRoll;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteLaborWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Human Resource Manager', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Financial Manager', 'guard_name' => 'web']);
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create(['role' => 'Administrator']);
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_complete_site_casual_labor_and_muster_roll_lifecycle(): void
    {
        $admin = $this->actingAdmin();

        $project = Project::create([
            'name' => 'Bole Commercial Complex Tower',
            'location' => 'Addis Ababa, Bole Sub-City',
            'budget' => 45000000.00,
            'status' => 'in_progress',
        ]);

        // 2. Register Casual Laborers
        $this->actingAs($admin);

        $worker1Response = $this->post(route('labor.workers.store'), [
            'first_name' => 'Abebe',
            'middle_name' => 'Kebede',
            'last_name' => 'Tadesse',
            'phone' => '0911223344',
            'id_card_number' => 'KB-48291',
            'trade' => 'Mason',
            'skill_level' => 'skilled',
            'daily_wage_rate' => 500.00,
            'overtime_hourly_rate' => 78.13,
            'project_id' => $project->id,
            'notes' => 'Experienced stone and block mason',
        ]);

        $worker1Response->assertRedirect();
        $this->assertDatabaseHas('casual_laborers', [
            'first_name' => 'Abebe',
            'last_name' => 'Tadesse',
            'trade' => 'Mason',
            'daily_wage_rate' => 500.00,
            'is_active' => true,
        ]);

        $worker1 = CasualLaborer::where('first_name', 'Abebe')->first();
        $this->assertNotNull($worker1);
        $this->assertStringStartsWith('LAB-', $worker1->worker_code);
        $this->assertEquals('Abebe Kebede Tadesse', $worker1->full_name);

        $worker2 = CasualLaborer::create([
            'worker_code' => CasualLaborer::generateWorkerCode(),
            'first_name' => 'Chala',
            'last_name' => 'Bekele',
            'phone' => '0922334455',
            'trade' => 'Bar Bender / Steel Fixer',
            'skill_level' => 'skilled',
            'daily_wage_rate' => 400.00,
            'overtime_hourly_rate' => 62.50,
            'project_id' => $project->id,
            'is_active' => true,
        ]);

        // 3. Test Quick List API
        $quickListResponse = $this->getJson(route('labor.workers.quick-list', ['project_id' => $project->id]));
        $quickListResponse->assertOk();
        $quickListData = $quickListResponse->json();
        $this->assertCount(2, $quickListData);

        // 4. Create and Submit Daily Muster Roll
        $musterRollData = [
            'project_id' => $project->id,
            'date' => '2026-09-26',
            'title' => '2nd Floor Column Shuttering & Rebar Fixing',
            'supervisor_id' => $admin->id,
            'notes' => 'Smooth site operation. Overtime approved for concrete prep.',
            'status' => 'submitted',
            'items' => [
                [
                    'casual_laborer_id' => $worker1->id,
                    'trade' => 'Mason',
                    'attendance_status' => 'full_day',
                    'days_worked' => 1.0,
                    'daily_rate' => 500.00,
                    'overtime_hours' => 2.0,
                    'overtime_rate' => 78.13,
                    'deduction_amount' => 0.00,
                    'task_assigned' => 'Hollow block laying at axis 4-7',
                ],
                [
                    'casual_laborer_id' => $worker2->id,
                    'trade' => 'Bar Bender / Steel Fixer',
                    'attendance_status' => 'half_day',
                    'days_worked' => 0.5,
                    'daily_rate' => 400.00,
                    'overtime_hours' => 0.0,
                    'overtime_rate' => 62.50,
                    'deduction_amount' => 50.00, // Advance deduction
                    'task_assigned' => 'Beam rebar tying',
                ],
            ],
        ];

        $rollStoreResponse = $this->post(route('labor.muster-rolls.store'), $musterRollData);
        $roll = MusterRoll::orderByDesc('id')->first();
        $this->assertNotNull($roll);
        $rollStoreResponse->assertRedirect(route('labor.muster-rolls.show', $roll->id));

        // Math Verification:
        // Worker 1: 1.0 * 500 = 500 regular, 2 * 78.13 = 156.26 OT, 0 deduction -> net = 656.26
        // Worker 2: 0.5 * 400 = 200 regular, 0 OT, 50 deduction -> net = 150.00
        // Grand regular = 700.00
        // Grand OT = 156.26
        // Grand deductions = 50.00
        // Grand Net = 700.00 + 156.26 - 50.00 = 806.26
        $this->assertEquals(2, $roll->total_workers);
        $this->assertEquals(700.00, (float) $roll->total_regular_amount);
        $this->assertEquals(156.26, (float) $roll->total_overtime_amount);
        $this->assertEquals(50.00, (float) $roll->total_deductions);
        $this->assertEquals(806.26, (float) $roll->total_net_amount);
        $this->assertEquals('submitted', $roll->status);

        // 5. Approve Muster Roll
        $approveResponse = $this->post(route('labor.muster-rolls.approve', $roll->id));
        $approveResponse->assertRedirect();
        $roll->refresh();
        $this->assertEquals('approved', $roll->status);
        $this->assertEquals($admin->id, $roll->approved_by);
        $this->assertNotNull($roll->approved_at);

        // 6. Disburse Wage Payout (Automatic Project Expense Creation)
        $payoutResponse = $this->post(route('labor.muster-rolls.payout', $roll->id), [
            'payment_method' => 'telebirr',
            'payout_reference' => 'TB-90281-SITE',
        ]);
        $payoutResponse->assertRedirect();
        $roll->refresh();

        $this->assertEquals('paid', $roll->status);
        $this->assertEquals('telebirr', $roll->payment_method);
        $this->assertEquals('TB-90281-SITE', $roll->payout_reference);
        $this->assertNotNull($roll->paid_at);
        $this->assertNotNull($roll->expense_id);

        // Verify that the expense is cleanly posted to the project
        $expense = Expense::find($roll->expense_id);
        $this->assertNotNull($expense);
        $this->assertEquals($project->id, $expense->project_id);
        $this->assertEquals(806.26, (float) $expense->amount);
        $this->assertEquals('Labor', $expense->category);
        $this->assertEquals($roll->muster_roll_no, $expense->reference_no);
        $this->assertEquals('approved', $expense->status);

        // Verify project aggregate casual labor cost
        $project->refresh();
        $this->assertEquals(806.26, (float) $project->total_casual_labor_cost);

        // 7. Verify Public Field QR Verification Endpoint
        $verifyResponse = $this->get(route('verify.muster-roll', $roll->muster_roll_no));
        $verifyResponse->assertOk();
        $verifyResponse->assertSee($roll->muster_roll_no);
        $verifyResponse->assertSee('806.26 ETB');
        $verifyResponse->assertSee('Abebe Kebede Tadesse');

        // 8. Verify Corporate Printable Sheet
        $printResponse = $this->get(route('labor.muster-rolls.print', $roll->id));
        $printResponse->assertOk();
        $printResponse->assertSee('DAILY MUSTER ROLL');
        $printResponse->assertSee('NATANEM ENGINEERING PLC');
        $printResponse->assertSee('806.26 ETB');
    }
}
