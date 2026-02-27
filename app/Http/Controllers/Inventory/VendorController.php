<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display the vendor registry.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');

        $vendors = Vendor::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'ILIKE', "%{$q}%")
                    ->orWhere('code', 'ILIKE', "%{$q}%")
                    ->orWhere('contact_person', 'ILIKE', "%{$q}%")
                    ->orWhere('email', 'ILIKE', "%{$q}%");
            })
            ->latest()
            ->paginate(20);

        return view('inventory.vendors.index', compact('vendors', 'q'));
    }

    /**
     * Show form to add a new vendor.
     */
    public function create()
    {
        return view('inventory.vendors.create');
    }

    /**
     * Store the new vendor record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:100',
        ]);

        $vendor = Vendor::create($validated);

        return redirect()->route('inventory.vendors.index')
            ->with('success', "Vendor '{$vendor->name}' has been successfully onboarded.");
    }

    /**
     * Show vendor details.
     */
    public function show(Vendor $vendor)
    {
        return view('inventory.vendors.show', compact('vendor'));
    }

    /**
     * Show form to edit vendor metadata.
     */
    public function edit(Vendor $vendor)
    {
        return view('inventory.vendors.edit', compact('vendor'));
    }

    /**
     * Update vendor metadata.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        // Fix for checkbox boolean if missing
        $validated['is_active'] = $request->has('is_active');

        $vendor->update($validated);

        return redirect()->route('inventory.vendors.index')
            ->with('success', "Vendor '{$vendor->name}' metadata updated.");
    }

    /**
     * Remove vendor from active registry.
     */
    public function destroy(Vendor $vendor)
    {
        $name = $vendor->name;
        $vendor->delete();

        return redirect()->route('inventory.vendors.index')
            ->with('success', "Vendor '{$name}' has been removed from registry.");
    }
}
