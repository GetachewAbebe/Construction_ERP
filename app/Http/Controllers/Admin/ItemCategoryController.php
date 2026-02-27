<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = ItemCategory::with('parent')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.item_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $parentCategories = ItemCategory::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.item_categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:item_categories,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        ItemCategory::create($validated);

        return redirect()->route('admin.item-categories.index')
            ->with('success', "Strategic asset classification '{$validated['name']}' has been registered in the system.");
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(ItemCategory $itemCategory)
    {
        $parentCategories = ItemCategory::whereNull('parent_id')
            ->where('id', '!=', $itemCategory->id) // Prevent self-nesting
            ->orderBy('name')
            ->get();

        return view('admin.item_categories.edit', compact('itemCategory', 'parentCategories'));
    }

    /**
     * Update the category.
     */
    public function update(Request $request, ItemCategory $itemCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name,'.$itemCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:item_categories,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $itemCategory->update($validated);

        return redirect()->route('admin.item-categories.index')
            ->with('success', "Asset classifier '{$itemCategory->name}' has been successfully reconfigured.");
    }

    /**
     * Remove the category.
     */
    public function destroy(ItemCategory $itemCategory)
    {
        $name = $itemCategory->name;

        // Prevent deletion if it has items or children?
        // For now, Eloquent soft-delete is fine, but maybe warn if it has items.
        if ($itemCategory->items()->count() > 0) {
            return back()->with('error', "Operational Lock: Cannot expunge '{$name}' while active inventory assets are linked to this classification.");
        }

        $itemCategory->delete();

        return redirect()->route('admin.item-categories.index')
            ->with('success', "Asset classification '{$name}' has been removed from active mapping.");
    }
}
