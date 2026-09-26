<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ClientPaymentCertificate;
use App\Models\ClientPaymentReceipt;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientBillingWorkflowTest extends TestCase
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

    public function test_complete_client_progress_billing_workflow(): void
    {
        $admin = $this->actingAdmin();

        $project = Project::create([
            'name' => 'Addis Smart City Tower',
            'location' => 'Bole Medhanialem',
            'status' => 'active',
            'budget' => 50000000.00,
        ]);

        // Step 1: Submit Client IPC #1 Claim
        // Gross work done = 5,000,000 ETB
        // Retention = 5% = 250,000 ETB
        // Advance Recoupment = 10% = 500,000 ETB
        // Net subtotal = 5,000,000 - 250,000 - 500,000 = 4,250,000 ETB
        // VAT 15% = 637,500 ETB
        // Total certified = 4,887,500 ETB
        $ipcResponse = $this->actingAs($admin)->post(route('finance.client-certificates.store'), [
            'project_id' => $project->id,
            'period_start' => now()->subDays(30)->toDateString(),
            'period_end' => now()->toDateString(),
            'submission_date' => now()->toDateString(),
            'client_name' => 'Addis Smart City Dev Corp',
            'consultant_name' => 'MH Engineering Consultants',
            'work_description' => 'Substructure excavation and foundation concrete pour',
            'cumulative_gross_amount' => 5000000.00,
            'previous_gross_amount' => 0.00,
            'materials_on_site' => 0.00,
            'retention_rate' => 5.00,
            'advance_recoupment_rate' => 10.00,
            'other_deductions' => 0.00,
            'tax_rate' => 15.00,
            'notes' => 'Tested concrete cube strengths passed 28-day requirement',
        ]);

        $ipc = ClientPaymentCertificate::where('project_id', $project->id)->firstOrFail();
        $ipcResponse->assertRedirect(route('finance.client-certificates.show', $ipc->id));

        $this->assertEquals(5000000.00, (float) $ipc->current_gross_amount);
        $this->assertEquals(250000.00, (float) $ipc->retention_deduction);
        $this->assertEquals(500000.00, (float) $ipc->advance_deduction);
        $this->assertEquals(4250000.00, (float) $ipc->subtotal_net_amount);
        $this->assertEquals(637500.00, (float) $ipc->tax_amount);
        $this->assertEquals(4887500.00, (float) $ipc->total_certified_amount);
        $this->assertEquals(4887500.00, (float) $ipc->balance_due);
        $this->assertEquals('submitted', $ipc->status);

        // Step 2: Supervising Consultant Certification
        $certifyResponse = $this->actingAs($admin)->post(route('finance.client-certificates.certify', $ipc->id));
        $certifyResponse->assertSessionHas('success');

        $ipc->refresh();
        $this->assertEquals('certified', $ipc->status);
        $this->assertEquals(now()->toDateString(), $ipc->certification_date->toDateString());

        // Step 3: Record Partial Client Payment Receipt (e.g. 2,000,000 ETB via CPO)
        $payment1Response = $this->actingAs($admin)->post(route('finance.client-certificates.payments.store', $ipc->id), [
            'amount' => 2000000.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'CPO',
            'bank_name' => 'Commercial Bank of Ethiopia',
            'transaction_reference' => 'CPO-991204',
            'notes' => 'First partial installment cleared',
        ]);

        $payment1Response->assertSessionHas('success');
        $ipc->refresh();

        $this->assertEquals(2000000.00, (float) $ipc->amount_paid);
        $this->assertEquals(2887500.00, (float) $ipc->balance_due);
        $this->assertEquals('partially_paid', $ipc->status);

        // Step 4: Record Remaining Balance Payment (2,887,500 ETB via RTGS)
        $payment2Response = $this->actingAs($admin)->post(route('finance.client-certificates.payments.store', $ipc->id), [
            'amount' => 2887500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'Bank Transfer',
            'bank_name' => 'Awash Bank',
            'transaction_reference' => 'RTGS-774128',
            'notes' => 'Final claim installment settled in full',
        ]);

        $payment2Response->assertSessionHas('success');
        $ipc->refresh();

        $this->assertEquals(4887500.00, (float) $ipc->amount_paid);
        $this->assertEquals(0.00, (float) $ipc->balance_due);
        $this->assertEquals('paid', $ipc->status);

        // Step 5: Verify Project-level aggregates & retention tracking
        $project->refresh();
        $this->assertEquals(4887500.00, $project->total_certified_revenue);
        $this->assertEquals(250000.00, $project->total_retention_withheld);
        $this->assertEquals(4887500.00, $project->total_revenue_collected);

        // Step 6: Verify Corporate Printable View & QR verification
        $printResponse = $this->actingAs($admin)->get(route('prints.client-ipc', $ipc));
        $printResponse->assertOk()
            ->assertSee('INTERIM PAYMENT CERTIFICATE')
            ->assertSee('Addis Smart City Tower')
            ->assertSee('SCAN TO VERIFY');

        $verifyResponse = $this->get(route('verify.client-ipc', $ipc));
        $verifyResponse->assertOk()
            ->assertSee($ipc->certificate_no)
            ->assertSee('Addis Smart City Tower');
    }
}
