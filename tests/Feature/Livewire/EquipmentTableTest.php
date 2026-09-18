<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Equipment\EquipmentTable;
use App\Models\Equipment;
use App\Models\EquipmentLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EquipmentTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_table_renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EquipmentTable::class)
            ->assertOk()
            ->assertSee('Total Fleet Assets')
            ->assertSee('Operational');
    }

    public function test_can_create_new_machinery_equipment(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Bole Tower Construction',
            'status' => 'active',
            'budget' => 50000000,
        ]);

        Livewire::actingAs($user)
            ->test(EquipmentTable::class)
            ->call('create')
            ->set('name', 'Caterpillar 320D Excavator')
            ->set('plate_number', 'ET-3-99120')
            ->set('equipment_type', 'Excavator')
            ->set('project_id', $project->id)
            ->set('equipment_status', 'operational')
            ->set('operating_hours', 550.0)
            ->set('next_service_hours', 600.0)
            ->set('fuel_type', 'Diesel')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $equipment = Equipment::where('plate_number', 'ET-3-99120')->first();
        $this->assertNotNull($equipment);
        $this->assertSame('Caterpillar 320D Excavator', $equipment->name);
        $this->assertEquals(550.0, (float) $equipment->operating_hours);
        $this->assertSame($project->id, $equipment->project_id);
    }

    public function test_can_log_service_and_update_operating_hours(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::create([
            'name' => 'Komatsu Loader',
            'plate_number' => 'ET-3-11223',
            'type' => 'Loader',
            'status' => 'operational',
            'operating_hours' => 800.0,
            'next_service_hours' => 750.0,
            'fuel_type' => 'Diesel',
        ]);

        $this->assertTrue($equipment->isServiceOverdue());

        Livewire::actingAs($user)
            ->test(EquipmentTable::class)
            ->call('openLogModal', $equipment->id)
            ->set('log_type', 'service')
            ->set('hours_at_log', 850.0)
            ->set('cost', 12500.0)
            ->set('description', 'Oil and hydraulic filters replaced')
            ->set('logged_at', now()->toDateString())
            ->call('saveLog')
            ->assertHasNoErrors()
            ->assertSet('showLogModal', false);

        $equipment->refresh();
        $this->assertEquals(850.0, (float) $equipment->operating_hours);

        $log = EquipmentLog::where('equipment_id', $equipment->id)->first();
        $this->assertNotNull($log);
        $this->assertSame('service', $log->log_type);
        $this->assertEquals(12500.0, (float) $log->cost);
    }
}
