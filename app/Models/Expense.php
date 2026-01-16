<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Project;
use App\Models\User;
use App\Traits\LogsActivity;

class Expense extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'project_id',
        'user_id',
        'amount',
        'category',
        'description',
        'expense_date',
        'reference_no',
        'status',
        'approved_by',
        'rejected_by',
        'rejection_reason',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
