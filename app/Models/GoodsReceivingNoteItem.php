<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivingNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receiving_note_id',
        'purchase_order_item_id',
        'inventory_item_id',
        'item_name',
        'unit_of_measurement',
        'quantity_delivered',
        'quantity_accepted',
        'quantity_rejected',
        'rejection_reason',
    ];

    protected $casts = [
        'quantity_delivered' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
    ];

    public function goodsReceivingNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivingNote::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
