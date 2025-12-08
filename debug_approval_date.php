<?php
use App\Models\InventoryLoan;

echo "Checking Loans...\n";
$loans = InventoryLoan::all();
foreach ($loans as $loan) {
    echo "Loan #{$loan->id} Status: {$loan->status}\n";
    echo " - Approved At (Raw): " . ($loan->getAttributes()['approved_at'] ?? 'NULL') . "\n";
    echo " - Approved At (Cast): " . ($loan->approved_at ? $loan->approved_at->format('Y-m-d H:i') : 'NULL') . "\n";
}
