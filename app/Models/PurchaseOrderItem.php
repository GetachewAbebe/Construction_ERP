<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'inventory_item_id',
        'item_name',
        'unit_of_measurement',
        'quantity',
        'unit_price',
        'total_price',
        'quantity_received',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity_received' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function receivingItems(): HasMany
    {
        return $this->hasMany(GoodsReceivingNoteItem::class, 'purchase_order_item_id');
    }

    public function getQuantityRemainingAttribute(): float
    {
        return max(0, (float) $this->quantity - (float) $this->quantity_received);
    }
}
