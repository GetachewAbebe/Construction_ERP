<?php

namespace App\Services;

use App\Models\AssetClassification;
use Illuminate\Support\Str;

class AssetClassificationService
{
    /**
     * Create a new classification with standardized data.
     */
    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['code'] = strtoupper($data['code'] ?? $this->generateDefaultCode($data['name']));

        return AssetClassification::create($data);
    }

    /**
     * Update an existing classification and propagate hierarchy changes if needed.
     */
    public function update(AssetClassification $classification, array $data)
    {
        if (isset($data['name']) && $data['name'] !== $classification->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $classification->update($data);

        // If parent changed, we might need to update all children.
        // Eloquent 'updating' hook in model handles some, but for deep trees we might need recursion.
        if ($classification->wasChanged('parent_id')) {
            $this->rebuildChildPaths($classification);
        }

        return $classification;
    }

    /**
     * Generate a professional default code from name.
     */
    private function generateDefaultCode(string $name): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $name);

        return strtoupper(substr($clean, 0, 4));
    }

    /**
     * Recursively rebuild hierarchy paths for descendants.
     */
    private function rebuildChildPaths(AssetClassification $parent)
    {
        foreach ($parent->children as $child) {
            $child->updateHierarchyInfo();
            $this->rebuildChildPaths($child);
        }
    }
}
