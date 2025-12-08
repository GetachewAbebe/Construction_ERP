<?php
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;

echo "Creating dummy data...\n";

// Create Department
$dept = Department::firstOrCreate(['name' => 'Engineering']);
echo "Department: {$dept->name} (ID: {$dept->id})\n";

// Create Position
$pos = Position::firstOrCreate(['title' => 'Senior Developer']);
echo "Position: {$pos->title} (ID: {$pos->id})\n";

// Update Employee
$emp = Employee::first();
if ($emp) {
    try {
        $emp->department_id = $dept->id;
        $emp->position_id = $pos->id;
        $emp->save();
        echo "Updated Employee {$emp->name} with Dept ID {$dept->id} and Pos ID {$pos->id}\n";
        
        // Reload and check accessors
        $emp->refresh();
        echo "Check Accessors:\n";
        echo " - Department: {$emp->department}\n";
        echo " - Position: {$emp->position}\n";
    } catch (\Exception $e) {
        echo "Error updating: " . $e->getMessage() . "\n";
    }
} else {
    echo "No employee found.\n";
}
