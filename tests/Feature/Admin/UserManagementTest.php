<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        Role::findOrCreate('Administrator', 'web');
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_admin_can_create_new_user_and_linked_employee(): void
    {
        $admin = $this->actingAdmin();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Michael Scott',
                'email' => 'michael.scott@example.com',
                'password' => 'password123',
                'role' => 'Administrator',
                'phone_number' => '12345678',
                'position' => 'Regional Manager',
                'department' => 'Management',
                'status' => 'Active',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.users.index'));

        // Verify user exists
        $user = User::where('email', 'michael.scott@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Michael', $user->first_name);
        $this->assertSame('Scott', $user->last_name);

        // Verify linked employee exists
        $employee = Employee::where('user_id', $user->id)->first();
        $this->assertNotNull($employee);
        $this->assertSame('michael.scott@example.com', $employee->email);
        $this->assertSame('Michael', $employee->first_name);
        $this->assertSame('Scott', $employee->last_name);
    }

    public function test_admin_can_update_user_and_sync_employee(): void
    {
        $admin = $this->actingAdmin();
        $user = User::factory()->create([
            'name' => 'Jim Halpert',
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
            'email' => 'jim.halpert@example.com',
            'role' => 'FinancialManager',
        ]);
        // Seed corresponding role to prevent validation issues or sync issues
        Role::findOrCreate('FinancialManager', 'web');

        // Create linked employee
        $employee = Employee::create([
            'user_id' => $user->id,
            'first_name' => 'Jim',
            'last_name' => 'Halpert',
            'email' => 'jim.halpert@example.com',
            'hire_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Jim Bernard Halpert',
                'email' => 'jim.halpert@example.com',
                'role' => 'FinancialManager',
                'phone_number' => '87654321',
                'position' => 'Senior Sales Representative',
                'department' => 'Sales',
                'status' => 'Active',
                'bio' => 'Likes pulling pranks on Dwight',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Jim', $user->first_name);
        $this->assertSame('Bernard', $user->middle_name);
        $this->assertSame('Halpert', $user->last_name);
        $this->assertSame('Likes pulling pranks on Dwight', $user->bio);

        $employee->refresh();
        $this->assertSame('Senior Sales Representative', $employee->position);
        $this->assertSame('Sales', $employee->department);
    }

    public function test_admin_can_soft_delete_user(): void
    {
        $admin = $this->actingAdmin();
        $user = User::factory()->create([
            'name' => 'Pam Beesly',
            'first_name' => 'Pam',
            'last_name' => 'Beesly',
            'email' => 'pam.beesly@example.com',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted($user);
    }
}
