<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes, LogsActivity;


    protected $fillable = [
        'name',
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
        $this->attributes['name'] = $value;
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

    /**
     * Industry Standard: Centralized Routing Logic
     * Returns the appropriate dashboard route name for the user.
     */
    public function getDashboardRouteName(): string
    {
        // 1. Spatie Roles (Primary - matching production DB)
        if ($this->hasRole('Administrator') || $this->hasRole('Admin')) return 'admin.dashboard';
        if ($this->hasRole('Human Resource Manager')) return 'hr.dashboard';
        if ($this->hasRole('Inventory Manager')) return 'inventory.dashboard';
        if ($this->hasRole('Financial Manager')) return 'finance.dashboard';
        
        // 2. Exact Model Trait names (Secondary fallback)
        if ($this->hasRole('HumanResourceManager')) return 'hr.dashboard';
        if ($this->hasRole('InventoryManager')) return 'inventory.dashboard';
        if ($this->hasRole('FinancialManager')) return 'finance.dashboard';

        // 3. Database Column Fallback (Insurance for mismatching production keys)
        $columnRole = strtolower(trim((string)$this->role));
        if (str_contains($columnRole, 'admin')) return 'admin.dashboard';
        if (str_contains($columnRole, 'hr') || str_contains($columnRole, 'human')) return 'hr.dashboard';
        if (str_contains($columnRole, 'inventory')) return 'inventory.dashboard';
        if (str_contains($columnRole, 'finance')) return 'finance.dashboard';

        // 4. Force Logout to break redirect loop
        Auth::logout();
        return 'home';
    }

    /**
     * Returns the direct URL to the user's profile/edit page.
     */
    public function getProfileUrl(): string
    {
        return route($this->getProfileRouteName('show'));
    }

    /**
     * Determines the correct profile route name based on the user's role.
     */
    public function getProfileRouteName(string $action = 'show'): string
    {
        $role = strtolower(trim((string)$this->role));
        
        $prefix = 'admin'; // Fallback
        if (str_contains($role, 'admin')) $prefix = 'admin';
        elseif (str_contains($role, 'human') || str_contains($role, 'hr')) $prefix = 'hr';
        elseif (str_contains($role, 'inventory')) $prefix = 'inventory';
        elseif (str_contains($role, 'finance') || str_contains($role, 'financial')) $prefix = 'finance';

        // Match the route names defined in web.php
        return "{$prefix}.profile.{$action}";
    }

    /**
     * Centralized Notification Counts by Module
     */
    public function getUnreadCountByModule(string $module): int
    {
        $types = [
            'hr'        => ['leave_request', 'leave_status', 'attendance_alert'],
            'inventory' => ['inventory_request', 'inventory_status', 'stock_alert'],
            'finance'   => ['expense_request', 'expense_status', 'budget_alert'],
        ];

        if (!isset($types[$module])) return 0;

        // POSTGRES COMPATIBLE FIX:
        // Use whereJsonContains which works seamlessly across MySQL and Postgres
        // Or if 'data' is text, we cannot use ->> operator directly without ensuring column type.
        // Assuming 'data' is JSONB or JSON column on notifications table (Laravel Standard).
        // If it is TEXT, we must cast:
        // return $this->unreadNotifications()->whereRaw("data::jsonb->>'type' in (?,?,?)", ...)
        // However, Laravel's whereJsonContains is the safest abstraction usually.
        // BUT 'whereIn' on a JSON key is tricky.
        
        // Simpler fallback: Filter in PHP collection if volume is low (usually unread count < 100).
        // Or use proper Json query.
        
        // Attempting PHP-side filtering for robust cross-driver compatibility:
        return $this->unreadNotifications->filter(function ($notification) use ($types, $module) {
            return in_array($notification->data['type'] ?? '', $types[$module]);
        })->count();
    }

    /**
     * System-Wide Avatar Resolution
     * Centralizes profile picture logic for the entire platform.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->employee ? $this->employee->profile_picture_url : null;
    }

    /**
     * Generate a URL-friendly slug based on role and name.
     */
    public function getSlugAttribute(): string
    {
        $rolePart = str_replace(' ', '-', strtolower($this->role ?? 'user'));
        $namePart = str_replace(' ', '-', strtolower($this->name));
        return "{$rolePart}-{$namePart}";
    }
}
