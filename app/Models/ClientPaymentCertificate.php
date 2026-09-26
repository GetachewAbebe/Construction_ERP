<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPaymentCertificate extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'certificate_no',
        'project_id',
        'ipc_sequence',
        'period_start',
        'period_end',
        'submission_date',
        'certification_date',
        'payment_due_date',
        'client_name',
        'consultant_name',
        'work_description',
        'cumulative_gross_amount',
        'previous_gross_amount',
        'current_gross_amount',
        'materials_on_site',
        'retention_rate',
        'retention_deduction',
        'advance_recoupment_rate',
        'advance_deduction',
        'other_deductions',
        'subtotal_net_amount',
        'tax_rate',
        'tax_amount',
        'total_certified_amount',
        'amount_paid',
        'balance_due',
        'status',
        'prepared_by',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'submission_date' => 'date',
        'certification_date' => 'date',
        'payment_due_date' => 'date',
        'cumulative_gross_amount' => 'decimal:2',
        'previous_gross_amount' => 'decimal:2',
        'current_gross_amount' => 'decimal:2',
        'materials_on_site' => 'decimal:2',
        'retention_rate' => 'decimal:2',
        'retention_deduction' => 'decimal:2',
        'advance_recoupment_rate' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'subtotal_net_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_certified_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'ipc_sequence' => 'integer',
    ];

    public static function generateCertificateNo(Project $project): string
    {
        $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->name), 0, 3));
        if (strlen($code) < 3) {
            $code = 'PRJ';
        }

        $nextSeq = static::withTrashed()->where('project_id', $project->id)->count() + 1;
        $formattedSeq = str_pad((string) $nextSeq, 2, '0', STR_PAD_LEFT);

        return "IPC-{$code}-{$formattedSeq}";
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(ClientPaymentReceipt::class)->orderByDesc('payment_date');
    }

    public function refreshPaymentStatus(): void
    {
        $totalPaid = (float) $this->receipts()->sum('amount');
        $totalCertified = (float) $this->total_certified_amount;
        $balanceDue = max(0, round($totalCertified - $totalPaid, 2));

        $status = $this->status;
        if ($totalPaid >= $totalCertified && $totalCertified > 0) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partially_paid';
        }

        $this->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $balanceDue,
            'status' => $status,
        ]);
    }
}
