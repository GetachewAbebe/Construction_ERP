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
 * @property int|null $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $name
 * @property string $email
 * @property string|null $department
 * @property int|null $department_id
 * @property string|null $position
 * @property int|null $position_id
 * @property string|null $phone
 * @property string|null $profile_picture
 * @property \Illuminate\Support\Carbon|null $hire_date
 * @property \Illuminate\Support\Carbon|null $contract_end_date
 * @property string|null $employment_type
 * @property string|null $status
 * @property string|null $salary
 * @property string|null $tin_number
 * @property string|null $pension_number
 * @property string|null $bank_name
 * @property string|null $bank_account
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Department|null $department_rel
 * @property-read Position|null $position_rel
 * @property-read User|null $user
 */
class Employee extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'department',
        'department_id',
        'position',
        'position_id',
        'hire_date',
        'contract_end_date',
        'employment_type',
        'status',
        'salary',
        'tin_number',
        'pension_number',
        'bank_name',
        'bank_account',
        'profile_picture',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department_rel(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position_rel(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the employee's full name.
     */
    public function getNameAttribute()
    {
        // If the 'name' column exists and is not empty, return it.
        if (! empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        // Otherwise construct from first/last name
        $first = $this->attributes['first_name'] ?? '';
        $last = $this->attributes['last_name'] ?? '';

        return trim("{$first} {$last}") ?: 'N/A';
    }

    /**
     * Get the employee's full name (alias for name).
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get Department Name
     */
    public function getDepartmentAttribute()
    {
        // If the raw attribute exists (legacy string column), use it
        if (! empty($this->attributes['department'])) {
            return $this->attributes['department'];
        }

        // Use the relationship (Benefit: Supports Eager Loading)
        return $this->department_rel ? $this->department_rel->name : 'N/A';
    }

    /**
     * Set Department Name (resolves to department_id)
     */
    public function setDepartmentAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['department_id'] = null;

            return;
        }

        $dept = \App\Models\Department::firstOrCreate(['name' => $value]);
        $this->attributes['department_id'] = $dept->id;
    }

    /**
     * Get Position Title
     */
    public function getPositionAttribute()
    {
        // If the raw attribute exists (legacy string column), use it
        if (! empty($this->attributes['position'])) {
            return $this->attributes['position'];
        }

        // Use the relationship (Benefit: Supports Eager Loading)
        return $this->position_rel ? $this->position_rel->title : 'N/A';
    }

    /**
     * Set Position Title (resolves to position_id)
     */
    public function setPositionAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['position_id'] = null;

            return;
        }

        $pos = \App\Models\Position::firstOrCreate(['title' => $value]);
        $this->attributes['position_id'] = $pos->id;
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * PREMIUM SOLUTION: Get profile picture URL with intelligent fallback
     * Works regardless of symlink configuration or server setup
     */
    public function getProfilePictureUrlAttribute()
    {
        $path = trim((string) $this->profile_picture, ' /\\');
        if (! $path) {
            return null;
        }

        // Use standard Storage URL if path is valid
        // This leverages the /storage/ symlink which is more reliable in most browsers
        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);

        // Add cache-busting timestamp
        $timestamp = $this->updated_at ? $this->updated_at->timestamp : time();

        return $url.'?v='.$timestamp;
    }

    /**
     * Check if employee has a profile picture
     */
    public function hasProfilePicture()
    {
        return ! empty($this->profile_picture) && $this->profile_picture_url !== null;
    }
}
