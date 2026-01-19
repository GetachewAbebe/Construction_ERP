<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetClassification;
use App\Services\AssetClassificationService;
use Illuminate\Http\Request;

class AssetClassificationController extends Controller
{
    protected $service;

    public function __construct(AssetClassificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the strategic classification registry.
     */
    public function index()
    {
        $classifications = AssetClassification::with('parent')
            ->orderBy('hierarchy_path')
            ->paginate(25);

        return view('admin.asset_classifications.index', compact('classifications'));
    }

    /**
     * Provision a new classification level.
     */
    public function create()
    {
        $parents = AssetClassification::orderBy('name')->get();
        return view('admin.asset_classifications.create', compact('parents'));
    }

    /**
     * Register the new classification in the system.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:asset_classifications,name',
            'code'            => 'nullable|string|max:10|unique:asset_classifications,code',
            'description'     => 'nullable|string',
            'icon_identifier' => 'nullable|string|max:50',
            'parent_id'       => 'nullable|exists:asset_classifications,id',
        ]);

        $this->service->create($validated);

        return redirect()->route('inventory.asset-classifications.index')
            ->with('success', "Category '{$validated['name']}' has been created successfully.");
    }

    /**
     * Reconfigure metadata for a classification.
     */
    public function edit(AssetClassification $assetClassification)
    {
        $parents = AssetClassification::where('id', '!=', $assetClassification->id)
            ->where('hierarchy_path', 'NOT LIKE', $assetClassification->hierarchy_path . '%')
            ->orderBy('name')
            ->get();

        return view('admin.asset_classifications.edit', [
            'classification' => $assetClassification,
            'parents'        => $parents,
        ]);
    }

    /**
     * Persist the reconfigured metadata.
     */
    public function update(Request $request, AssetClassification $assetClassification)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255|unique:asset_classifications,name,' . $assetClassification->id,
            'code'            => 'required|string|max:10|unique:asset_classifications,code,' . $assetClassification->id,
            'description'     => 'nullable|string',
            'icon_identifier' => 'nullable|string|max:50',
            'parent_id'       => 'nullable|exists:asset_classifications,id',
        ]);

        $this->service->update($assetClassification, $validated);

        return redirect()->route('inventory.asset-classifications.index')
            ->with('success', "Category '{$assetClassification->name}' has been updated.");
    }

    /**
     * Decommission a classification from active service.
     */
    public function destroy(AssetClassification $assetClassification, Request $request)
    {
        $name = $assetClassification->name;

        if ($assetClassification->assets()->count() > 0) {
            return back()->with('error', "Cannot delete category '{$name}' because it contains active inventory items.");
        }

        if ($assetClassification->children()->count() > 0) {
            return back()->with('error', "Cannot delete category '{$name}' because it has sub-categories. Please move or delete them first.");
        }

        $assetClassification->delete();

        return redirect()->route('inventory.asset-classifications.index')
            ->with('success', "Category '{$name}' has been deleted.");
    }
}
