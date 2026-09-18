<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Projects\DailyReportsTable;
use App\Models\DailyProgressReport;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DailyReportsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_reports_table_renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DailyReportsTable::class)
            ->assertOk()
            ->assertSee('Total Filed DPRs');
    }

    public function test_can_submit_daily_progress_report(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Sarbet Commercial Complex',
            'status' => 'active',
            'budget' => 80000000,
        ]);

        Livewire::actingAs($user)
            ->test(DailyReportsTable::class)
            ->call('create')
            ->set('project_id', $project->id)
            ->set('report_date', now()->toDateString())
            ->set('weather', 'Sunny')
            ->set('manpower_count', 38)
            ->set('work_performed', 'Casting foundation columns and retaining wall waterproofing.')
            ->set('materials_received', '500 Bags Dangote 42.5 cement')
            ->set('machinery_deployed', 'CAT 320D Excavator, 2 Mixers')
            ->set('safety_incidents', 'Zero incidents.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $dpr = DailyProgressReport::where('project_id', $project->id)->first();
        $this->assertNotNull($dpr);
        $this->assertSame(38, $dpr->manpower_count);
        $this->assertSame('Sunny', $dpr->weather);
        $this->assertEquals($user->id, $dpr->user_id);
    }
}
