<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $project_id
 * @property string $name
 * @property string $trade
 * @property string|null $contract_number
 * @property float $contract_sum
 * @property string|null $contact_person
 * @property string|null $phone
 * @property string|null $email
 * @property string $status
 * @property string|null $notes
 * @property-read Project|null $project
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubcontractorCertificate> $certificates
 */
class Subcontractor extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'subcontractors';

    protected $fillable = [
        'project_id',
        'name',
        'trade',
        'contract_number',
        'contract_sum',
        'contact_person',
        'phone',
        'email',
        'status',
        'notes',
    ];

    protected $casts = [
        'contract_sum' => 'decimal:2',
    ];

    protected $appends = [
        'total_certified',
        'total_retained',
        'remaining_balance',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(SubcontractorCertificate::class);
    }

    public function getTotalCertifiedAttribute(): float
    {
        return (float) $this->certificates()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('gross_amount');
    }

    public function getTotalRetainedAttribute(): float
    {
        return (float) $this->certificates()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('retention_amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0.0, (float) $this->contract_sum - $this->total_certified);
    }
}
