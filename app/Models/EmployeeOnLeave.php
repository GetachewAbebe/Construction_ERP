<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOnLeave extends Model
{
    protected $table = 'employees_on_leave';

    protected $fillable = [
        'employee_id','start_date','end_date','reason','approved_by','approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'approved_at'=> 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
