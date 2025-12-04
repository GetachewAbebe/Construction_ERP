<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // present but unused in forms
        'first_name',
        'last_name',
        'email',
        'department_id',  // kept null for now (no UI mapping provided)
        'position_id',    // kept null for now
        'hire_date',
        'salary',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Convenience accessor if you want to show full name
    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }
}
