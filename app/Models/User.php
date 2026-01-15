<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;


    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone_number',
        'position',
        'department',
        'status',
        'bio',
    ];

    /**
     * Get the user's full name.
     */
    public function getNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Set the user's full name (helper).
     */
    public function setNameAttribute($value)
    {
        $parts = explode(' ', $value);
        $this->attributes['first_name'] = array_shift($parts);
        $this->attributes['last_name']  = array_pop($parts);
        $this->attributes['middle_name'] = implode(' ', $parts);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
