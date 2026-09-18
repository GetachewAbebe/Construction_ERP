<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DailyProgressReport;
use App\Models\Equipment;
use App\Models\EquipmentLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentAndReportsSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        $admin = User::role('Administrator')->first() ?? User::first();

        $equipmentData = [
            [
                'name' => 'Caterpillar 320D Hydraulic Excavator',
                'plate_number' => 'ET-3-88210',
                'type' => 'Excavator',
                'project_id' => $project?->id,
                'status' => 'operational',
                'operating_hours' => 1420.50,
                'next_service_hours' => 1500.00,
                'fuel_type' => 'Diesel',
                'purchase_date' => '2023-04-15',
                'purchase_cost' => 8500000.00,
                'notes' => 'Primary excavation unit for foundation and trenching works.',
            ],
            [
                'name' => 'Komatsu WA380 Wheel Loader',
                'plate_number' => 'ET-3-67432',
                'type' => 'Loader',
                'project_id' => $project?->id,
                'status' => 'operational',
                'operating_hours' => 980.00,
                'next_service_hours' => 1000.00,
                'fuel_type' => 'Diesel',
                'purchase_date' => '2023-08-20',
                'purchase_cost' => 6200000.00,
                'notes' => 'Aggregate stockpile handling and site clearing.',
            ],
            [
                'name' => 'Mercedes-Benz Actros 3340 Transit Mixer (8m³)',
                'plate_number' => 'ET-3-11904',
                'type' => 'Concrete Mixer',
                'project_id' => $project?->id,
                'status' => 'maintenance',
                'operating_hours' => 2450.00,
                'next_service_hours' => 2400.00,
                'fuel_type' => 'Diesel',
                'purchase_date' => '2022-11-10',
                'purchase_cost' => 4800000.00,
                'notes' => 'Scheduled for drum roller bearing and chute overhaul.',
            ],
            [
                'name' => 'Cummins 250kVA Silent Diesel Generator',
                'plate_number' => 'GEN-04',
                'type' => 'Generator',
                'project_id' => $project?->id,
                'status' => 'operational',
                'operating_hours' => 620.00,
                'next_service_hours' => 750.00,
                'fuel_type' => 'Diesel',
                'purchase_date' => '2024-01-05',
                'purchase_cost' => 1950000.00,
                'notes' => 'Site backup power for batching plant and welding machines.',
            ],
            [
                'name' => 'Sinotruk Howo 6x4 Heavy Tipper Dump Truck (18m³)',
                'plate_number' => 'ET-3-55012',
                'type' => 'Dump Truck',
                'project_id' => $project?->id,
                'status' => 'operational',
                'operating_hours' => 3100.00,
                'next_service_hours' => 3250.00,
                'fuel_type' => 'Diesel',
                'purchase_date' => '2022-06-18',
                'purchase_cost' => 3400000.00,
                'notes' => 'Bulk earthwork haulage and gravel transport.',
            ],
        ];

        foreach ($equipmentData as $data) {
            $eq = Equipment::updateOrCreate(['plate_number' => $data['plate_number']], $data);

            // Add sample service log
            EquipmentLog::updateOrCreate(
                [
                    'equipment_id' => $eq->id,
                    'logged_at' => now()->subDays(10)->toDateString(),
                ],
                [
                    'user_id' => $admin?->id,
                    'log_type' => 'service',
                    'hours_at_log' => $eq->operating_hours - 50.00,
                    'cost' => 18500.00,
                    'description' => '250-Hour preventive maintenance: Replaced engine oil, fuel filters, hydraulic return filter, and lubed chassis points.',
                ]
            );
        }

        // Add sample DPR if project exists
        if ($project) {
            DailyProgressReport::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'report_date' => now()->toDateString(),
                ],
                [
                    'user_id' => $admin?->id,
                    'weather' => 'Clear / Sunny',
                    'manpower_count' => 42,
                    'work_performed' => "1. Completed 2nd floor slab concrete casting (Grid A-1 to D-8) with 85m³ C-25 concrete.\n2. Reinforcement steel fixing for 3rd floor shear walls and columns.\n3. Ground floor external blockwork masonry completed on eastern elevation.",
                    'materials_received' => '1. 300 Bags Dangote PPC 42.5 Cement (Waybill #8812)\n2. 12 Tons D16/D20 High-yield Rebar\n3. 40m³ River Sand',
                    'machinery_deployed' => '1 CAT 320D Excavator, 2 Transit Mixers, 1 Concrete Boom Pump, 1 250kVA Generator',
                    'safety_incidents' => 'Conducted morning toolbox talk on working at height & safety harnesses. Zero accidents or near-misses reported.',
                    'status' => 'submitted',
                ]
            );
        }
    }
}
