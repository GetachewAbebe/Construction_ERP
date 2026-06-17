<?php

declare(strict_types=1);

namespace Tests\Feature\Finance;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseBudgetTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        Role::findOrCreate('Administrator', 'web');
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_expense_exceeding_remaining_budget_is_rejected(): void
    {
        $user = $this->actingAdmin();
        $project = Project::create(['name' => 'Site A', 'budget' => 1000, 'status' => 'In Progress']);
        $project->expenses()->create(['category' => 'materials', 'amount' => 800, 'expense_date' => now()->toDateString()]);

        // 800 already spent; 300 more would exceed the 1000 budget.
        $this->actingAs($user)
            ->post(route('finance.expenses.store'), [
                'project_id' => $project->id,
                'category' => 'labor',
                'amount' => 300,
                'expense_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('amount');

        $this->assertSame(1, $project->expenses()->count());
    }

    public function test_expense_within_remaining_budget_is_accepted(): void
    {
        $user = $this->actingAdmin();
        $project = Project::create(['name' => 'Site B', 'budget' => 1000, 'status' => 'In Progress']);
        $project->expenses()->create(['category' => 'materials', 'amount' => 800, 'expense_date' => now()->toDateString()]);

        // 800 spent + 200 = exactly the 1000 budget → allowed.
        $this->actingAs($user)
            ->post(route('finance.expenses.store'), [
                'project_id' => $project->id,
                'category' => 'labor',
                'amount' => 200,
                'expense_date' => now()->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, $project->expenses()->count());
    }
}
