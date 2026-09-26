<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\EthiopianCalendarService;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $project_id
 * @property int|null $milestone_id
 * @property string $title
 * @property string|null $document_code
 * @property string $document_type
 * @property string $revision_number
 * @property string $file_path
 * @property string $file_name
 * @property int $file_size
 * @property string $mime_type
 * @property int $uploaded_by
 * @property string $status
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $description
 * @property string|null $ethiopian_date_text
 * @property-read Project $project
 * @property-read ProjectMilestone|null $milestone
 * @property-read User $uploader
 * @property-read User|null $approver
 * @property-read string $formatted_file_size
 * @property-read string $file_url
 */
class ProjectDocument extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'project_id',
        'milestone_id',
        'title',
        'document_code',
        'document_type',
        'revision_number',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'status',
        'approved_by',
        'approved_at',
        'description',
        'ethiopian_date_text',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_file_size',
        'file_url',
        'ethiopian_created_at',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (ProjectDocument $doc) {
            if (empty($doc->ethiopian_date_text)) {
                $doc->ethiopian_date_text = EthiopianCalendarService::formatDual(now());
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (float) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getEthiopianCreatedAtAttribute(): string
    {
        if ($this->created_at) {
            return EthiopianCalendarService::formatDual($this->created_at);
        }

        return EthiopianCalendarService::formatDual(now());
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }
}
