<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DailyProgressReport;
use App\Models\Equipment;
use App\Models\EquipmentLog;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\SubcontractorCertificate;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnterpriseFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Financial Manager', 'guard_name' => 'web']);
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create(['role' => 'Administrator']);
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_can_log_daily_progress_report_with_photos(): void
    {
        Storage::fake('public');
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Bole Commercial Tower',
            'code' => 'BCT-01',
            'status' => 'in_progress',
            'budget' => 5000000.00,
        ]);

        $photo1 = UploadedFile::fake()->create('slab_pour.jpg', 100, 'image/jpeg');
        $photo2 = UploadedFile::fake()->create('rebar_inspection.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('projects.daily-reports.store'), [
            'project_id' => $project->id,
            'report_date' => now()->toDateString(),
            'weather' => 'Sunny / Clear',
            'manpower_count' => 28,
            'work_performed' => 'Casting C-25 concrete slab for 4th floor.',
            'materials_received' => '300 bags Mugher Cement',
            'machinery_deployed' => '1x Concrete Pump, 2x Vibrators',
            'safety_incidents' => 'None',
            'photos' => [$photo1, $photo2],
        ]);

        $response->assertRedirect(route('projects.daily-reports.index'));

        $this->assertDatabaseHas('daily_progress_reports', [
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'manpower_count' => 28,
        ]);

        $report = DailyProgressReport::first();
        $this->assertNotNull($report);
        $this->assertIsArray($report->photos);
        $this->assertCount(2, $report->photos);
    }

    public function test_can_render_printable_dpr_view(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Hawassa Industrial Shed',
            'code' => 'HIS-02',
            'status' => 'in_progress',
            'budget' => 2500000.00,
        ]);

        $report = DailyProgressReport::create([
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'report_date' => now()->toDateString(),
            'weather' => 'Clear',
            'manpower_count' => 14,
            'work_performed' => 'Roof truss installation and welding inspection.',
            'photos' => ['/storage/reports/sample.jpg'],
        ]);

        $response = $this->actingAs($admin)->get(route('projects.daily-reports.print', $report->id));

        $response->assertOk();
        $response->assertSee('DAILY PROGRESS REPORT');
        $response->assertSee('Roof truss installation');
        $response->assertSee('Hawassa Industrial Shed');
    }

    public function test_qr_code_service_generates_valid_svg(): void
    {
        $qr = new QrCodeService();
        $svg = $qr->svg('https://erp.natanemengineering.com/verify/gate-pass/42', 150);

        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', $svg);
        $this->assertStringContainsString('width="150"', $svg);
    }

    public function test_can_verify_gate_pass_via_mobile_endpoint(): void
    {
        $admin = $this->actingAdmin();
        $item = InventoryItem::create([
            'name' => 'DeWalt Demolition Hammer',
            'item_code' => 'TL-001',
            'category' => 'Tools',
            'quantity' => 10,
            'unit_of_measurement' => 'pcs',
            'unit_cost' => 15000,
            'status' => 'available',
        ]);

        $employee = \App\Models\Employee::create([
            'first_name' => 'Abebe',
            'last_name' => 'Kebede',
            'email' => 'abebe@natanemengineering.com',
            'status' => 'Active',
            'hire_date' => now()->toDateString(),
        ]);

        $loan = InventoryLoan::create([
            'inventory_item_id' => $item->id,
            'employee_id' => $employee->id,
            'requested_by_user_id' => $admin->id,
            'quantity' => 1,
            'loan_date' => now()->toDateString(),
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        // Public/mobile verification screen
        $response = $this->get(route('verify.gate-pass', $loan->id));
        $response->assertOk();
        $response->assertSee('DeWalt Demolition Hammer');
        $response->assertSee('Official Authentic Gate Pass');

        // Site supervisor confirms delivery
        $confirmResponse = $this->actingAs($admin)->post(route('verify.gate-pass.confirm', $loan->id));
        $confirmResponse->assertSessionHas('success');

        $loan->refresh();
        $this->assertStringContainsString('Confirmed received at project site', (string) $loan->notes);
    }

    public function test_can_verify_expense_voucher_via_mobile_endpoint(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Gerji Luxury Apartments',
            'code' => 'GLA-03',
            'status' => 'in_progress',
            'budget' => 8000000.00,
        ]);

        $expense = Expense::create([
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'amount' => 75000.00,
            'category' => 'Materials',
            'expense_date' => now()->toDateString(),
            'description' => 'Procurement of structural steel beams 16mm.',
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);

        $response = $this->get(route('verify.expense-voucher', $expense->id));
        $response->assertOk();
        $response->assertSee('ETB 75,000.00');
        $response->assertSee('Gerji Luxury Apartments');
        $response->assertSee('Legitimate Corporate Voucher');
    }

    public function test_equipment_fuel_efficiency_and_service_status(): void
    {
        $admin = $this->actingAdmin();
        $eq = Equipment::create([
            'name' => 'Komatsu D65 Dozer',
            'plate_number' => 'ET-03-5511',
            'type' => 'Bulldozer',
            'status' => 'operational',
            'operating_hours' => 100.00,
            'next_service_hours' => 110.00, // within 25 hours -> due_soon
        ]);

        $this->assertSame('due_soon', $eq->service_status);

        // Log fuel refill
        EquipmentLog::create([
            'equipment_id' => $eq->id,
            'user_id' => $admin->id,
            'log_type' => 'fuel',
            'hours_at_log' => 100.00,
            'fuel_liters' => 500.00,
            'cost' => 45000.00,
            'description' => '500L diesel refill',
            'logged_at' => now()->toDateString(),
        ]);

        $eq->refresh();
        $this->assertEquals(5.00, $eq->fuel_efficiency); // 500L / 100hrs = 5.00 L/hr
    }

    public function test_can_register_subcontractor_and_issue_ipc(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Lebu Residential Villas',
            'code' => 'LRV-04',
            'status' => 'in_progress',
            'budget' => 4000000.00,
        ]);

        // 1. Register Subcontractor
        $response = $this->actingAs($admin)->post(route('contracts.subcontractors.store'), [
            'project_id' => $project->id,
            'name' => 'Nile Finishing Works PLC',
            'trade' => 'Plastering & Painting',
            'contract_sum' => 600000.00,
            'contact_person' => 'Ato Dawit',
            'phone' => '+251911223344',
        ]);

        $response->assertRedirect(route('contracts.subcontractors.index'));
        $sub = Subcontractor::first();
        $this->assertNotNull($sub);
        $this->assertSame('Nile Finishing Works PLC', $sub->name);

        // 2. Issue Interim Payment Certificate (IPC)
        $certResponse = $this->actingAs($admin)->post(route('contracts.subcontractors.certificates.store', $sub->id), [
            'period_start' => now()->subDays(20)->toDateString(),
            'period_end' => now()->toDateString(),
            'work_description' => '1,200 m2 gypsum plastering completed on block B.',
            'gross_amount' => 100000.00,
            'retention_percent' => 5.00,
        ]);

        $certResponse->assertRedirect(route('contracts.subcontractors.index'));

        $cert = SubcontractorCertificate::first();
        $this->assertNotNull($cert);
        $this->assertEquals(100000.00, (float) $cert->gross_amount);
        $this->assertEquals(5000.00, (float) $cert->retention_amount); // 5% of 100,000
        $this->assertEquals(95000.00, (float) $cert->net_payable); // 100,000 - 5,000
    }
}
