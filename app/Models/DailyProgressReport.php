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
 * @property int $project_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $report_date
 * @property string|null $weather
 * @property int|null $manpower_count
 * @property string|null $work_performed
 * @property string|null $materials_received
 * @property string|null $machinery_deployed
 * @property string|null $safety_incidents
 * @property string|null $status
 * @property-read Project|null $project
 * @property-read User|null $author
 */
class DailyProgressReport extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<self>> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'daily_progress_reports';

    protected $fillable = [
        'project_id',
        'user_id',
        'report_date',
        'weather',
        'manpower_count',
        'work_performed',
        'materials_received',
        'machinery_deployed',
        'safety_incidents',
        'photos',
        'status',
    ];

    protected $casts = [
        'report_date' => 'date',
        'manpower_count' => 'integer',
        'photos' => 'array',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
