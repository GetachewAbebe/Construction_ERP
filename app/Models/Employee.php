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
        
        // Direct DB Query (Single File Approach)
        if (!empty($this->attributes['department_id'])) {
            return \Illuminate\Support\Facades\DB::table('departments')
                ->where('id', $this->attributes['department_id'])
                ->value('name') ?? 'N/A';
        }
        
        return 'N/A';
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
        
        // Direct DB Query (Single File Approach)
        if (!empty($this->attributes['position_id'])) {
            return \Illuminate\Support\Facades\DB::table('positions')
                ->where('id', $this->attributes['position_id'])
                ->value('title') ?? 'N/A';
        }
        
        return 'N/A';
    }
    /**
     * Get the attendances for the employee.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
