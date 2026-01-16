<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'employee_id','start_date','end_date','reason','status','approved_by','approved_at'
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
