<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreVendorRequest;
use App\Http\Requests\Inventory\UpdateVendorRequest;
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
    public function store(StoreVendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());

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
    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $vendor->update($request->validated());

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
