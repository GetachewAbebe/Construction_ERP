<?php
use Illuminate\Support\Facades\Schema;

echo "--- SCHEMA INFO ---\n";
$columns = Schema::getColumnListing('employees');
foreach ($columns as $col) {
    $type = Schema::getColumnType('employees', $col);
    echo "$col: $type\n";
}
echo "--- END SCHEMA ---\n";
