<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected InventoryItem $item;

    /**
     * Create a new notification instance.
     */
    public function __construct(InventoryItem $item)
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $unit = $this->item->unit_of_measurement ?? 'units';
        $qty = $this->item->quantity;
        $isZero = $qty <= 0;

        return [
            'type' => 'inventory_low_stock',
            'title' => $isZero ? 'Out of Stock Alert' : 'Low Stock Warning',
            'message' => $isZero
                ? "Item '{$this->item->name}' is completely out of stock (0 {$unit}). Restock immediately."
                : "Item '{$this->item->name}' is running critically low ({$qty} {$unit} remaining).",
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'quantity' => $qty,
            'unit' => $unit,
            'url' => route('inventory.items.index'),
            'icon' => 'bi-exclamation-triangle',
            'color' => $isZero ? 'danger' : 'warning',
            'priority' => 'high',
        ];
    }
}

