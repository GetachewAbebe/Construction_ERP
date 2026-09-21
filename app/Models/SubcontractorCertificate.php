<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $subcontractor_id
 * @property int|null $project_id
 * @property int|null $approved_by
 * @property string $certificate_number
 * @property \Illuminate\Support\Carbon $period_start
 * @property \Illuminate\Support\Carbon $period_end
 * @property string $work_description
 * @property float $gross_amount
 * @property float $retention_percent
 * @property float $retention_amount
 * @property float $net_payable
 * @property string $status
 * @property string|null $rejection_reason
 * @property-read Subcontractor|null $subcontractor
 * @property-read Project|null $project
 * @property-read User|null $approvedBy
 */
class SubcontractorCertificate extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'subcontractor_certificates';

    protected $fillable = [
        'subcontractor_id',
        'project_id',
        'approved_by',
        'certificate_number',
        'period_start',
        'period_end',
        'work_description',
        'gross_amount',
        'retention_percent',
        'retention_amount',
        'net_payable',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'gross_amount' => 'decimal:2',
        'retention_percent' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'net_payable' => 'decimal:2',
    ];

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
