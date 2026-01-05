<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\InventoryItem;

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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject('Low Stock Alert: ' . $this->item->name)
                    ->line('The following item has reached a low stock level:')
                    ->line('Item No: ' . $this->item->item_no)
                    ->line('Item Name: ' . $this->item->name)
                    ->line('Current Quantity: ' . $this->item->quantity)
                    ->action('View Item', route('inventory.items.edit', $this->item->id))
                    ->line('Please consider restock soon.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id'   => $this->item->id,
            'item_name' => $this->item->name,
            'quantity'  => $this->item->quantity,
            'message'   => "Low stock alert for {$this->item->name} (Qty: {$this->item->quantity})",
        ];
    }
}
