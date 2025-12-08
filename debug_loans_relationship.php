
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "\nChecking 'employees' table schema:\n";
$columns = Schema::getColumnListing('employees');
echo "Columns: " . implode(', ', $columns) . "\n";

echo "\nChecking column types:\n";
foreach ($columns as $col) {
    try {
        $type = Schema::getColumnType('employees', $col);
        echo " - $col: $type\n";
    } catch (\Exception $e) {
        echo " - $col: (error getting type)\n";
    }
}


foreach ($loans as $loan) {
    echo "Loan ID: {$loan->id}, Employee ID: {$loan->employee_id}\n";
    $employee = $loan->employee;
    if ($employee) {
        echo " - Employee Found: ID {$employee->id}, Name: '{$employee->name}'\n";
    } else {
        echo " - Employee NOT FOUND for ID {$loan->employee_id}\n";
    }
}




echo "\nChecking ALL Employees Table Data:\n";
$allEmployees = Employee::all();
foreach ($allEmployees as $emp) {
    echo "ID: {$emp->id} | Attributes: " . json_encode($emp->getAttributes()) . "\n";
}



