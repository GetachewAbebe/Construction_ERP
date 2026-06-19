<?php

declare(strict_types=1);

namespace Tests\Feature\Finance;

use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        Role::findOrCreate('Administrator', 'web');
        Role::findOrCreate('FinancialManager', 'web');
        Role::findOrCreate('Financial Manager', 'web');
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_expense_can_be_approved(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create(['name' => 'Site C', 'budget' => 5000, 'status' => 'In Progress']);
        $expense = Expense::create([
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'category' => 'materials',
            'amount' => 1000,
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.finance.approve', $expense))
            ->assertSessionHas('status')
            ->assertRedirect();

        $expense->refresh();
        $this->assertSame('approved', $expense->status);
        $this->assertEquals($admin->id, $expense->approved_by);
    }

    public function test_expense_can_be_rejected_with_reason(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create(['name' => 'Site D', 'budget' => 5000, 'status' => 'In Progress']);
        $expense = Expense::create([
            'project_id' => $project->id,
            'user_id' => $admin->id,
            'category' => 'labor',
            'amount' => 2000,
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.finance.reject', $expense), [
                'rejection_reason' => 'Over budget limit for this week',
            ])
            ->assertSessionHas('status')
            ->assertRedirect();

        $expense->refresh();
        $this->assertSame('rejected', $expense->status);
        $this->assertEquals($admin->id, $expense->rejected_by);
        $this->assertSame('Over budget limit for this week', $expense->rejection_reason);
    }
}
