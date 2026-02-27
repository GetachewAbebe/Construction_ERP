<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetClassification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'icon_identifier',
        'parent_id',
        'hierarchy_path',
        'depth',
    ];

    /**
     * Boot: Auto-generate slug and update hierarchy path.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->code)) {
                $model->code = strtoupper(Str::limit(preg_replace('/[^A-Z]/', '', strtoupper($model->name)), 4, ''));
            }
        });

        static::created(function ($model) {
            $model->updateHierarchyInfo();
        });

        static::updating(function ($model) {
            if ($model->isDirty('parent_id')) {
                $model->updateHierarchyInfo();
            }
        });
    }

    /**
     * Relationship: Parent Classification
     */
    public function parent()
    {
        return $this->belongsTo(AssetClassification::class, 'parent_id');
    }

    /**
     * Relationship: Child Classifications
     */
    public function children()
    {
        return $this->hasMany(AssetClassification::class, 'parent_id');
    }

    /**
     * Relationship: Linked Inventory Assets
     */
    public function assets()
    {
        return $this->hasMany(InventoryItem::class, 'classification_id');
    }

    /**
     * Update hierarchy path and depth based on parent.
     */
    public function updateHierarchyInfo()
    {
        if ($this->parent_id) {
            $parent = $this->parent;
            $this->hierarchy_path = ($parent->hierarchy_path ?: '').$this->id.'/';
            $this->depth = $parent->depth + 1;
        } else {
            $this->hierarchy_path = $this->id.'/';
            $this->depth = 0;
        }

        // Use saveQuietly to avoid infinite loop
        $this->saveQuietly();
    }

    /**
     * Get the full breadcrumb nomenclature (e.g., Materials > Raw > Cement)
     */
    public function getFullNomenclatureAttribute()
    {
        if (! $this->parent_id) {
            return $this->name;
        }

        return $this->parent->full_nomenclature.' › '.$this->name;
    }

    /**
     * Total recursive asset count (this level + children)
     */
    public function getRecursiveAssetCountAttribute()
    {
        // Simple implementation: count where hierarchy_path starts with this level path
        return InventoryItem::whereHas('classification', function ($query) {
            $query->where('hierarchy_path', 'LIKE', $this->hierarchy_path.'%');
        })->count();
    }
}
