<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_payment_certificate_id',
        'receipt_no',
        'payment_date',
        'amount',
        'payment_method',
        'bank_name',
        'transaction_reference',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function generateReceiptNo(): string
    {
        $year = date('Y');
        $prefix = "CRCP-{$year}-";
        $last = static::where('receipt_no', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('receipt_no');

        if ($last && preg_match('/CRCP-\d{4}-(\d+)/', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(ClientPaymentCertificate::class, 'client_payment_certificate_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
