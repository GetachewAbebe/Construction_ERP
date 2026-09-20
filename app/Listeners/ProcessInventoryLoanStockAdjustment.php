<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\InventoryLoanApproved;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProcessInventoryLoanStockAdjustment
{
    public function handle(InventoryLoanApproved $event): void
    {
        $loan = $event->loan;
        $item = $loan->item;

        if ($item && $loan->quantity > 0) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_items', 'quantity')) {
                    $item->decrement('quantity', (int) $loan->quantity);
                }

                InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => (int) $loan->quantity,
                    'reason' => "Dispatched under Loan #{$loan->id}",
                ]);
            } catch (\Exception $e) {
                Log::warning('Loan stock deduction warning: '.$e->getMessage());
            }
        }
    }
}
