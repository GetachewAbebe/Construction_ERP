<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory; // SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'contact_person',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'country',
        'tax_id',
        'vat_registration_no',
        'payment_terms',
        'bank_details',
        'category',
        'rating',
        'is_active',
        'internal_notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'decimal:2',
    ];

    /**
     * Boot: Auto-generate code and slug if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->code)) {
                $model->code = $model->generateUniqueCode($model->name);
            }
        });
    }

    /**
     * Generate a professional unique code (e.g., VEN-NAME-001)
     */
    public function generateUniqueCode(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Z]/', '', strtoupper($name)), 0, 3));
        if (strlen($prefix) < 3) $prefix = 'VEN';
        
        $count = static::where('code', 'LIKE', $prefix . '-%')->count() + 1;
        $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Ensure real uniqueness
        while (static::where('code', $code)->exists()) {
            $count++;
            $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }

    /**
     * Scope: Active vendors only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
