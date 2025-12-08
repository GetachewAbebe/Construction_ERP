<?php
use App\Models\Employee;

echo "Checking Employee Data (Single File Approach)...\n";

$emp = Employee::with('user')->first();
if ($emp) {
    echo "Employee: {$emp->name}\n";
    echo "Department Accessor: {$emp->department}\n";
    echo "Position Accessor: {$emp->position}\n";
} else {
    echo "No employee found.\n";
}
