<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Record a manual change to an inventory item's quantity.
     */
    public function adjustQuantity(InventoryItem $item, int $newQuantity, string $reason, ?string $remarks = null): void
    {
        $previousQuantity = $item->quantity;
        $changeAmount = $newQuantity - $previousQuantity;

        if ($changeAmount === 0) {
            $item->update(['quantity' => $newQuantity]); // Still update in case other fields changed

            return;
        }

        DB::transaction(function () use ($item, $newQuantity, $previousQuantity, $changeAmount, $reason, $remarks) {
            $item->update(['quantity' => $newQuantity]);

            InventoryLog::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'change_amount' => $changeAmount,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason,
                'remarks' => $remarks,
            ]);

            $this->checkLowStock($item);
        });
    }

    /**
     * Specifically for loans where quantity decreases.
     */
    public function logLoanChange(InventoryItem $item, int $amount, string $reason, ?string $remarks = null): void
    {
        $previousQuantity = $item->quantity;
        $newQuantity = $previousQuantity + $amount; // $amount should be negative for deductions

        DB::transaction(function () use ($item, $newQuantity, $previousQuantity, $amount, $reason, $remarks) {
            $item->update(['quantity' => $newQuantity]);

            InventoryLog::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'change_amount' => $amount,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason,
                'remarks' => $remarks,
            ]);

            $this->checkLowStock($item);
        });
    }

    /**
     * Check if an item is low on stock and trigger notification.
     */
    protected function checkLowStock(InventoryItem $item): void
    {
        $threshold = config('inventory.low_stock_threshold', 5);

        if ($item->quantity <= $threshold) {
            // Find all administrators
            $admins = \App\Models\User::role('Administrator')->get();

            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($item));
            }
        }
    }
}
