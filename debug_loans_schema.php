<?php
use Illuminate\Support\Facades\Schema;

echo "Checking inventory_loans schema:\n";
$cols = Schema::getColumnListing('inventory_loans');
foreach ($cols as $c) {
    echo "$c: " . Schema::getColumnType('inventory_loans', $c) . "\n";
}
