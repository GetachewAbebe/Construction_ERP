<?php

$depts = \Illuminate\Support\Facades\DB::table('departments')->count();
$positions = \Illuminate\Support\Facades\DB::table('positions')->count();

echo "Departments count: $depts\n";
echo "Positions count: $positions\n";

if ($depts > 0) {
    echo "Sample Dept: " . \Illuminate\Support\Facades\DB::table('departments')->first()->name . "\n";
}
if ($positions > 0) {
    echo "Sample Pos: " . \Illuminate\Support\Facades\DB::table('positions')->first()->title . "\n";
}
