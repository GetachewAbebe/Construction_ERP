<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ItemCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
    ];

    /**
     * Boot the model to automatically generate slugs.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relationship: Parent category
     */
    public function parent()
    {
        return $this->belongsTo(ItemCategory::class, 'parent_id');
    }

    /**
     * Relationship: Child categories
     */
    public function children()
    {
        return $this->hasMany(ItemCategory::class, 'parent_id');
    }

    /**
     * Relationship: Items in this category
     */
    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'item_category_id');
    }

    /**
     * Total count of items (including children recursivelly if needed, for now just direct)
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }
}
