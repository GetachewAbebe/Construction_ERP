<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name', // Updated to match DB
        'last_name',  // Updated to match DB
        'name',       // Keeping for backward compatibility if needed, though DB seems to not have it
        'email',
        'department',
        'department_id', // Added
        'position',
        'position_id',   // Added
        'phone',
        'profile_picture', // Image path
        'hire_date',
        'salary',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department_rel()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function position_rel()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the employee's full name.
     */
    public function getNameAttribute()
    {
        // If the 'name' column exists and is not empty, return it.
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        // Otherwise construct from first/last name
        $first = $this->attributes['first_name'] ?? '';
        $last  = $this->attributes['last_name'] ?? '';
        
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
        if (!empty($this->attributes['department'])) {
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
        if (!empty($this->attributes['position'])) {
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
}
