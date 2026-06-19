<?php

declare(strict_types=1);

namespace Tests\Feature\HR;

use App\Models\Employee;
use App\Models\EmployeeOnLeave;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LeaveApprovalTest extends TestCase
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

    public function test_pending_leave_can_be_approved(): void
    {
        $admin = $this->actingAdmin();
        $employee = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'reason' => 'Medical leave',
            'status' => 'Pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.leave.approve', $leave))
            ->assertSessionHas('success')
            ->assertRedirect();

        $leave->refresh();
        $this->assertSame('Approved', $leave->status);
        $this->assertEquals($admin->id, $leave->approved_by);

        // Verify EmployeeOnLeave record exists
        $this->assertTrue(EmployeeOnLeave::where('employee_id', $employee->id)->exists());
    }

    public function test_pending_leave_can_be_rejected(): void
    {
        $admin = $this->actingAdmin();
        $employee = Employee::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'reason' => 'Vacation',
            'status' => 'Pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.leave.reject', $leave))
            ->assertSessionHas('success')
            ->assertRedirect();

        $leave->refresh();
        $this->assertSame('Rejected', $leave->status);
        $this->assertEquals($admin->id, $leave->approved_by);

        // Verify EmployeeOnLeave record does NOT exist
        $this->assertFalse(EmployeeOnLeave::where('employee_id', $employee->id)->exists());
    }

    public function test_already_processed_leave_cannot_be_re_approved(): void
    {
        $admin = $this->actingAdmin();
        $employee = Employee::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'reason' => 'Medical leave',
            'status' => 'Approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.leave.approve', $leave))
            ->assertSessionHas('error');
    }
}
