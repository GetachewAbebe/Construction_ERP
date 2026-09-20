<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        Role::findOrCreate('Administrator', 'web');
        Role::findOrCreate('HumanResourceManager', 'web');
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_admin_can_export_expenses_csv(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create(['name' => 'Site Alpha', 'budget' => 50000, 'status' => 'In Progress']);
        Expense::create([
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'category' => 'Materials',
            'amount' => 5000,
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
            'description' => 'Export test voucher',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('finance.expenses.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('expenses_export_', (string) $response->headers->get('content-disposition'));
    }

    public function test_hr_can_export_attendance_csv(): void
    {
        $admin = $this->actingAdmin();
        $employee = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('hr.attendance.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attendance_records_', (string) $response->headers->get('content-disposition'));
    }

    public function test_can_export_equipment_fleet_csv(): void
    {
        $admin = $this->actingAdmin();
        Equipment::create([
            'name' => 'CAT Excavator 320',
            'plate_number' => 'ET-03-9999',
            'type' => 'Excavator',
            'status' => 'operational',
            'operating_hours' => 120.50,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('equipment.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('equipment_fleet_telemetry_', (string) $response->headers->get('content-disposition'));
    }
}
