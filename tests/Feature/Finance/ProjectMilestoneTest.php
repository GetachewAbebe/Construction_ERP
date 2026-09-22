<?php

declare(strict_types=1);

namespace Tests\Feature\Finance;

use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectMilestoneTest extends TestCase
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

    private function actingFinancialManager(): User
    {
        $user = User::factory()->create(['role' => 'Financial Manager']);
        $user->assignRole('Financial Manager');

        return $user;
    }

    public function test_can_create_project_milestone(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Adama Wind Farm Roadway',
            'status' => 'in_progress',
            'budget' => 4500000.00,
        ]);

        $response = $this->actingAs($admin)->post(route('finance.projects.milestones.store', $project), [
            'title' => 'Subgrade Compaction & Earthwork',
            'wbs_code' => '1.0',
            'description' => 'Heavy compaction and subbase preparation for section A.',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'progress' => 25,
            'weight_pct' => 20,
            'allocated_budget' => 900000.00,
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('project_milestones', [
            'project_id' => $project->id,
            'title' => 'Subgrade Compaction & Earthwork',
            'wbs_code' => '1.0',
            'progress' => 25,
            'weight_pct' => 20.00,
            'status' => 'in_progress',
            'created_by' => $admin->id,
        ]);
    }

    public function test_milestone_validation_requires_title_and_valid_dates(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Test Project',
            'status' => 'active',
            'budget' => 100000.00,
        ]);

        $response = $this->actingAs($admin)->post(route('finance.projects.milestones.store', $project), [
            'title' => '',
            'start_date' => now()->addDays(10)->toDateString(),
            'due_date' => now()->toDateString(), // due date before start date
            'progress' => 150, // invalid progress > 100
        ]);

        $response->assertSessionHasErrors(['title', 'due_date', 'progress']);
    }

    public function test_updating_progress_syncs_status_and_project_completion(): void
    {
        $financeManager = $this->actingFinancialManager();
        $project = Project::create([
            'name' => 'Bole Commercial Complex',
            'status' => 'in_progress',
            'budget' => 10000000.00,
        ]);

        $milestone1 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 1: Deep Foundation Piling',
            'wbs_code' => '1.0',
            'start_date' => now()->subDays(60)->toDateString(),
            'due_date' => now()->subDays(10)->toDateString(),
            'progress' => 100,
            'weight_pct' => 40.00,
            'status' => 'completed',
        ]);

        $milestone2 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 2: Superstructure Framing',
            'wbs_code' => '2.0',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(60)->toDateString(),
            'progress' => 0,
            'weight_pct' => 60.00,
            'status' => 'pending',
        ]);

        // Initially: (100 * 40 + 0 * 60) / 100 = 40%
        $this->assertEquals(40.0, $project->fresh()->physical_progress_percentage);

        // Update milestone 2 progress to 50% via quick patch endpoint
        $response = $this->actingAs($financeManager)->patch(
            route('finance.projects.milestones.progress', [$project, $milestone2]),
            ['progress' => 50]
        );

        $response->assertRedirect();
        $milestone2->refresh();

        $this->assertEquals(50, $milestone2->progress);
        $this->assertEquals('in_progress', $milestone2->status);

        // New overall: (100 * 40 + 50 * 60) / 100 = 70.0%
        $this->assertEquals(70.0, $project->fresh()->physical_progress_percentage);

        // Complete milestone 2 to 100%
        $this->actingAs($financeManager)->patch(
            route('finance.projects.milestones.progress', [$project, $milestone2]),
            ['progress' => 100]
        );

        $milestone2->refresh();
        $this->assertEquals('completed', $milestone2->status);
        $this->assertNotNull($milestone2->completed_at);
        $this->assertEquals(100.0, $project->fresh()->physical_progress_percentage);
    }

    public function test_can_update_and_delete_milestone(): void
    {
        $admin = $this->actingAdmin();
        $project = Project::create([
            'name' => 'Gelan Logistics Warehouse',
            'status' => 'in_progress',
            'budget' => 2000000.00,
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Steel Truss Roofing',
            'wbs_code' => '3.0',
            'progress' => 10,
            'weight_pct' => 25,
            'status' => 'in_progress',
        ]);

        // Update details
        $updateResponse = $this->actingAs($admin)->put(
            route('finance.projects.milestones.update', [$project, $milestone]),
            [
                'title' => 'Structural Steel Truss & Cladding',
                'wbs_code' => '3.1',
                'weight_pct' => 30,
                'progress' => 45,
                'status' => 'in_progress',
            ]
        );

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('project_milestones', [
            'id' => $milestone->id,
            'title' => 'Structural Steel Truss & Cladding',
            'wbs_code' => '3.1',
            'weight_pct' => 30.00,
            'progress' => 45,
        ]);

        // Delete milestone
        $deleteResponse = $this->actingAs($admin)->delete(
            route('finance.projects.milestones.destroy', [$project, $milestone])
        );

        $deleteResponse->assertRedirect();
        $this->assertSoftDeleted('project_milestones', [
            'id' => $milestone->id,
        ]);
    }
}
