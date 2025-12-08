<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


echo "\nChecking 'departments' schema:\n";
$cols = Schema::getColumnListing('departments');
foreach ($cols as $c) echo "$c: " . Schema::getColumnType('departments', $c) . "\n";

echo "\nChecking 'positions' schema:\n";
$cols = Schema::getColumnListing('positions');
foreach ($cols as $c) echo "$c: " . Schema::getColumnType('positions', $c) . "\n";

